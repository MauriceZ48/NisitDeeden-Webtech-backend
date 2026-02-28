<?php

namespace App\Http\Controllers;

use App\Enums\RoundStatus;
use App\Http\Requests\ApplicationRoundRequest;
use App\Models\ApplicationCategory;
use App\Models\ApplicationRound;
use App\Repositories\ApplicationCategoryRepository;
use App\Repositories\ApplicationRoundRepository;
use Illuminate\Http\Request;

class ApplicationRoundController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        private ApplicationRoundRepository $roundRepo,
        private ApplicationCategoryRepository $categoryRepo,
    )
    {}

    public function index()
    {
        $rounds = $this->roundRepo->getAllOrdered();

        return view('rounds.index', ['rounds' => $rounds]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $expected = $this->roundRepo->getNextExpectedRound();

        return view('rounds.create', [
            'expectedYear' => $expected['year'],
            'expectedSemester' => $expected['semester']
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

        $this->roundRepo->create($request->validated());
        return redirect()->route('rounds.index')->with('success', 'Round created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationRound $applicationRound)
    {
        $categories = ApplicationCategory::whereHas('applications', function ($q) use ($applicationRound) {
            $q->where('application_round_id', $applicationRound->id)
                ->where('status', 'APPROVED_BY_COMMITTEE');
        })
            ->with(['applications' => function ($q) use ($applicationRound) {
                // 2. Load only the specific approved applications for this round
                $q->where('application_round_id', $applicationRound->id)
                    ->where('status', 'APPROVED_BY_COMMITTEE')
                    ->with('user');
            }])
            ->get();
        return view('rounds.show', [
            'applicationRound' => $applicationRound,
            'categories'  => $categories,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicationRound $applicationRound)
    {
        return view('rounds.edit', [
            'applicationRound' => $applicationRound,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationRoundRequest $request, ApplicationRound $applicationRound)
    {
        // Define them once at the top of the method
        $startTime = $request->date('start_time');
        $endTime = $request->date('end_time');
        $now = now();

        if ($request->status === RoundStatus::OPEN->value) {
            // 1. TIME GATE: Use variables
            if ($now->lt($startTime) || $now->gt($endTime)) {
                return back()->withErrors([
                    'status' => "Cannot open: current time must be between the start and end period."
                ])->withInput();
            }

            // 2. CONCURRENCY: One at a time
            if ($this->roundRepo->anotherRoundIsActive($applicationRound->id)) {

                return back()->withErrors(['status' => 'Another round is already open.']);
            }
        }

        // 3. NO OVERLAP: Reuse the same variables here!
        if ($this->roundRepo->isOverlapping($startTime, $endTime, $applicationRound->id)) {
            return back()->withErrors([
                'start_time' => 'These dates overlap with another existing round.'
            ])->withInput();
        }

        $applicationRound->update($request->validated());
        return redirect()->route('rounds.index')->with('success', 'Round updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationRound $applicationRound)
    {
        if ($applicationRound->applications()->count() > 0) {
            $applicationRound->delete();
            return redirect()->route('rounds.index')->with('warning', 'Round is soft-deleted. ' . $applicationRound->countApplications() . ' applications is affected.');
        }

        $applicationRound->forceDelete();

        return redirect()->route('rounds.index')->with('success', 'Round removed completely.');
    }
}
