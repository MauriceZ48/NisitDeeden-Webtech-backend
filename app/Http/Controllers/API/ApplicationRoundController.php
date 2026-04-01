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
            return response()->json(['message' => 'No active application round at the moment.'], 404);
        }

        if (now()->gt($activeRound->end_time)) {
            Cache::forget($cacheKey);
            return response()->json(['message' => 'No active application round at the moment.'], 404);
        }

        return new ApplicationRoundResource($activeRound);
    }

    public function getNextExpectedRound()
    {
        $expected = $this->roundRepo->getNextExpectedRound();

        if (!$expected) {
            return response()->json([
                'message' => 'Could not determine the next expected round.'
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
        // Define variables once at the top
        $startTime = $request->date('start_time');
        $endTime = $request->date('end_time');
        $now = now();

        // 1. SEQUENTIAL GUARD: Must be the correct next Year/Semester
        $expected = $this->roundRepo->getNextExpectedRound();
        if ($request->academic_year != $expected['year'] ||
            $request->semester != $expected['semester']->value) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['academic_year' => ["Next round must be {$expected['year']} Semester {$expected['semester']->value}."]]
            ], 422);
        }

        // 2. OPEN-SPECIFIC GUARDS: Only run these if status is OPEN
        if ($request->status === RoundStatus::OPEN->value) {

            // A. TIME GATE: now() must be within the period
            if ($now->lt($startTime) || $now->gt($endTime)) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['status' => ["Cannot create an OPEN round: current time must be between the start and end period."]]
                ], 422);
            }

            // B. CONCURRENCY: Only one active round allowed
            if ($this->roundRepo->anotherRoundIsActive()) {
                return response()->json([
                    'message' => "Cannot create an open round while another is already active.",
                ], 422);
            }
        }

        // 3. OVERLAP GUARD: Always check this for all rounds (including drafts)
        if ($this->roundRepo->isOverlapping($startTime, $endTime)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['start_time' => 'These dates overlap with an existing application round.']
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
            return response()->json(['message' => 'Unauthorized campus access.'], 403);
        }
        return new ApplicationRoundResource($applicationRound);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationRoundRequest $request, ApplicationRound $applicationRound)
    {
        if ($applicationRound->domain !== auth()->user()->domain) {
            return response()->json(['message' => 'Unauthorized campus access.'], 403);
        }

        $startTime = $request->date('start_time');
        $endTime = $request->date('end_time');
        $now = now();

        if ($request->status === RoundStatus::OPEN->value) {
            // 1. TIME GATE
            if ($now->lt($startTime) || $now->gt($endTime)) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['status' => ["Cannot open: current time must be between the start and end period."]]
                ], 422);
            }

            // 2. CONCURRENCY
            if ($this->roundRepo->anotherRoundIsActive($applicationRound->id)) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['status' => ['Another round is already open.']]
                ], 422);
            }
        }

        // 3. NO OVERLAP
        if ($this->roundRepo->isOverlapping($startTime, $endTime, $applicationRound->id)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['start_time' => ['These dates overlap with another existing round.']]
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
            return response()->json(['message' => 'Unauthorized campus access.'], 403);
        }

        if ($applicationRound->applications()->count() > 0) {
            $applicationRound->delete();
            Cache::forget($this->getActiveRoundCacheKey());
            return response()->json([
                'message' => 'Round is soft-deleted. ' . $applicationRound->countApplications() . ' applications is affected.',
                'type' => 'warning',
                'soft_deleted' => true
            ], 200);
        }

        $applicationRound->forceDelete();
        Cache::forget($this->getActiveRoundCacheKey());
        return response()->json(null, 204);
    }
}
