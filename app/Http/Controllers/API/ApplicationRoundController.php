<?php

namespace App\Http\Controllers\API;

use App\Enums\RoundStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRoundRequest;
use App\Http\Resources\ApplicationRoundResource;
use App\Models\ApplicationRound;
use App\Repositories\ApplicationRoundRepository;
use Illuminate\Support\Facades\Cache;

class ApplicationRoundController extends Controller
{

    public function __construct(
        private ApplicationRoundRepository $roundRepo
    ){}


    private function getActiveRoundCacheKey(): string
    {
        $domain = auth()->user()->domain?->value ?? 'default';
        return "application_round.active.domain.{$domain}";
    }

    public function index()
    {
        $rounds = $this->roundRepo->getAllOrderedInDomain();
        return ApplicationRoundResource::collection($rounds);
    }

    public function getActiveRound()
    {
        $cacheKey = $this->getActiveRoundCacheKey();

        $activeRound = Cache::remember($cacheKey, 60 * 60, function () {
            return $this->roundRepo->getActive();
        });

        if (!$activeRound) {
            return response()->json(['message' => 'ในขณะนี้ไม่มีรอบการรับสมัครที่เปิดใช้งานอยู่'], 404);
        }

        if (now()->gt($activeRound->end_time)) {
            Cache::forget($cacheKey);
            return response()->json(['message' => 'รอบการรับสมัครปัจจุบันสิ้นสุดเวลาการรับสมัครแล้ว'], 404);
        }

        return new ApplicationRoundResource($activeRound);
    }

    public function getNextExpectedRound()
    {
        $expected = $this->roundRepo->getNextExpectedRound();

        if (!$expected) {
            return response()->json([
                'message' => 'ไม่สามารถระบุรอบการรับสมัครถัดไปได้'
            ], 404);
        }

        return response()->json([
            'data' => [
                'expected_year' => $expected['year'],
                'expected_semester' => $expected['semester']
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApplicationRoundRequest $request)
    {
        $startTime = $request->date('start_time');
        $endTime = $request->date('end_time');
        $now = now();

        // 1. SEQUENTIAL GUARD: ตรวจสอบความถูกต้องของปีการศึกษา/ภาคการศึกษาถัดไป
        $expected = $this->roundRepo->getNextExpectedRound();
        if ($request->academic_year != $expected['year'] ||
            $request->semester != $expected['semester']->value) {
            return response()->json([
                'message' => 'ข้อมูลที่ระบุไม่ถูกต้อง',
                'errors' => ['academic_year' => ["รอบถัดไปต้องเป็นปีการศึกษา {$expected['year']} ภาคการศึกษาที่ {$expected['semester']->value} เท่านั้น"]]
            ], 422);
        }

        // 2. OPEN-SPECIFIC GUARDS: ตรวจสอบเฉพาะกรณีที่จะเปิดรอบทันที (OPEN)
        if ($request->status === RoundStatus::OPEN->value) {

            // A. TIME GATE: เวลาปัจจุบันต้องอยู่ระหว่างช่วงที่กำหนด
            if ($now->lt($startTime) || $now->gt($endTime)) {
                return response()->json([
                    'message' => 'ข้อมูลที่ระบุไม่ถูกต้อง',
                    'errors' => ['status' => ["ไม่สามารถสร้างรอบที่ 'เปิดใช้งาน' ได้: เวลาปัจจุบันต้องอยู่ระหว่างวันเริ่มและสิ้นสุดการรับสมัคร"]]
                ], 422);
            }

            // B. CONCURRENCY: เปิดได้แค่รอบเดียวต่อวิทยาเขต
            if ($this->roundRepo->anotherRoundIsActive()) {
                return response()->json([
                    'message' => "ไม่สามารถสร้างรอบใหม่ได้เนื่องจากมีรอบการรับสมัครอื่นกำลังเปิดใช้งานอยู่",
                ], 422);
            }
        }

        // 3. OVERLAP GUARD: ตรวจสอบการทับซ้อนของเวลา
        if ($this->roundRepo->isOverlapping($startTime, $endTime)) {
            return response()->json([
                'message' => 'ข้อมูลที่ระบุไม่ถูกต้อง',
                'errors' => ['start_time' => 'ช่วงเวลาที่เลือกทับซ้อนกับรอบการรับสมัครที่มีอยู่แล้ว']
            ], 422);
        }

        $data = $request->validated();
        $data['domain'] = auth()->user()->domain;

        $round = $this->roundRepo->create($data);
        Cache::forget($this->getActiveRoundCacheKey());
        return new ApplicationRoundResource($round);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationRound $applicationRound)
    {
        if ($applicationRound->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลของวิทยาเขตอื่น'], 403);
        }
        return new ApplicationRoundResource($applicationRound);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationRoundRequest $request, ApplicationRound $applicationRound)
    {
        if ($applicationRound->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลของวิทยาเขตอื่น'], 403);
        }

        $startTime = $request->date('start_time');
        $endTime = $request->date('end_time');
        $now = now();

        if ($request->status === RoundStatus::OPEN->value) {
            // 1. TIME GATE
            if ($now->lt($startTime) || $now->gt($endTime)) {
                return response()->json([
                    'message' => 'ข้อมูลที่ระบุไม่ถูกต้อง',
                    'errors' => ['status' => ["ไม่สามารถเปิดใช้งาน: เวลาปัจจุบันต้องอยู่ระหว่างช่วงเริ่มและสิ้นสุดการรับสมัคร"]]
                ], 422);
            }

            // 2. CONCURRENCY
            if ($this->roundRepo->anotherRoundIsActive($applicationRound->id)) {
                return response()->json([
                    'message' => 'ข้อมูลที่ระบุไม่ถูกต้อง',
                    'errors' => ['status' => ['มีรอบการรับสมัครอื่นเปิดใช้งานอยู่แล้ว']]
                ], 422);
            }
        }

        // 3. NO OVERLAP
        if ($this->roundRepo->isOverlapping($startTime, $endTime, $applicationRound->id)) {
            return response()->json([
                'message' => 'ข้อมูลที่ระบุไม่ถูกต้อง',
                'errors' => ['start_time' => ['ช่วงเวลาที่เลือกทับซ้อนกับรอบการรับสมัครอื่น']]
            ], 422);
        }

        $applicationRound->update($request->validated());
        Cache::forget($this->getActiveRoundCacheKey());
        return new ApplicationRoundResource($applicationRound);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationRound $applicationRound)
    {
        if ($applicationRound->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลของวิทยาเขตอื่น'], 403);
        }

        if ($applicationRound->applications()->count() > 0) {
            $applicationCount = $applicationRound->countApplications();
            $applicationRound->delete();
            Cache::forget($this->getActiveRoundCacheKey());
            return response()->json([
                'message' => "ย้ายรอบการรับสมัครลงถังขยะแล้ว โดยมีใบสมัครที่ได้รับผลกระทบจำนวน $applicationCount รายการ",
                'type' => 'warning',
                'soft_deleted' => true
            ], 200);
        }

        $applicationRound->forceDelete();
        Cache::forget($this->getActiveRoundCacheKey());
        return response()->json(null, 204);
    }
}
