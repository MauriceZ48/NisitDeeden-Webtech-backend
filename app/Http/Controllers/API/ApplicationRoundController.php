<?php

namespace App\Http\Controllers\API;

use App\Enums\RoundStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRoundRequest;
use App\Http\Resources\ApplicationRoundResource;
use App\Models\ApplicationRound;
use App\Repositories\ApplicationRoundRepository;

class ApplicationRoundController extends Controller
{

    public function __construct(
        private ApplicationRoundRepository $roundRepo
    ){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rounds = $this->roundRepo->getAllOrdered();
        return ApplicationRoundResource::collection($rounds);
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
            return back()->withErrors([
                'academic_year' => "Next round must be {$expected['year']} Semester {$expected['semester']->value}."
            ]);
        }

        // 2. OPEN-SPECIFIC GUARDS: Only run these if status is OPEN
        if ($request->status === RoundStatus::OPEN->value) {

            // A. TIME GATE: now() must be within the period
            if ($now->lt($startTime) || $now->gt($endTime)) {
                return back()->withErrors([
                    'status' => "Cannot create an OPEN round: current time must be between the start and end period."
                ])->withInput();
            }

            // B. CONCURRENCY: Only one active round allowed
            if ($this->roundRepo->anotherRoundIsActive()) {
                return back()->withErrors([
                    'status' => 'Cannot create an open round while another is already active.'
                ]);
            }
        }

        // 3. OVERLAP GUARD: Always check this for all rounds (including drafts)
        if ($this->roundRepo->isOverlapping($startTime, $endTime)) {
            return back()->withErrors([
                'start_time' => 'These dates overlap with an existing application round.'
            ])->withInput();
        }

        $round = $this->roundRepo->create($request->validated());
        return new ApplicationRoundResource($round);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationRound $applicationRound)
    {
        return new ApplicationRoundResource($applicationRound);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationRoundRequest $request, ApplicationRound $applicationRound)
    {
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
        return new ApplicationRoundResource($applicationRound);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationRound $applicationRound)
    {
        if ($applicationRound->applications()->count() > 0) {
            $applicationRound->delete();

            return response()->json([
                'message' => 'Round is soft-deleted. ' . $applicationRound->countApplications() . ' applications is affected.',
                'type' => 'warning',
                'soft_deleted' => true
            ], 200);
        }

        $applicationRound->forceDelete();

        return response()->json(null, 204);
    }
}
