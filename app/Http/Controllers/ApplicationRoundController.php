<?php

namespace App\Http\Controllers;

use App\Enums\RoundStatus;
use App\Http\Requests\ApplicationRoundRequest;
use App\Models\ApplicationRound;
use Illuminate\Http\Request;

class ApplicationRoundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rounds = ApplicationRound::withCount('applications')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('rounds.index', ['rounds' => $rounds]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $expected = $this->getNextExpectedRound();

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
        $expected = $this->getNextExpectedRound();
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
            if ($this->anotherRoundIsActive()) {
                return back()->withErrors([
                    'status' => 'Cannot create an open round while another is already active.'
                ]);
            }
        }

        // 3. OVERLAP GUARD: Always check this for all rounds (including drafts)
        if ($this->isOverlapping($startTime, $endTime)) {
            return back()->withErrors([
                'start_time' => 'These dates overlap with an existing application round.'
            ])->withInput();
        }

        ApplicationRound::create($request->validated());
        return redirect()->route('rounds.index')->with('success', 'Round created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ApplicationRound $applicationRound)
    {
        //
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
            if ($this->anotherRoundIsActive($applicationRound->id)) {
                return back()->withErrors(['status' => 'Another round is already open.']);
            }
        }

        // 3. NO OVERLAP: Reuse the same variables here!
        if ($this->isOverlapping($startTime, $endTime, $applicationRound->id)) {
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
        // 1. Safety Gate: If there is student data, NEVER delete.
        if ($applicationRound->applications()->count() > 0) {
            return back()->withErrors(['delete' => 'History exists: Cannot delete a round with applications.']);
        }

        // 2. Clear Path: If no student data exists, use Force Delete.
        $applicationRound->forceDelete();

        return redirect()->route('rounds.index')->with('success', 'Round removed completely.');
    }

    private function anotherRoundIsActive($excludeId = null): bool
    {
        return ApplicationRound::active()
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    private function getNextExpectedRound()
    {
        $lastRound = ApplicationRound::orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->first();

        if (!$lastRound) {
            return ['year' => now()->year, 'semester' => \App\Enums\Semester::FIRST];
        }

        if ($lastRound->semester === \App\Enums\Semester::FIRST) {
            return [
                'year' => $lastRound->academic_year,
                'semester' => \App\Enums\Semester::SECOND
            ];
        } else {
            return [
                'year' => $lastRound->academic_year + 1,
                'semester' => \App\Enums\Semester::FIRST
            ];
        }

    }

    private function isOverlapping($startTime, $endTime, $excludeId = null): bool
    {
        return ApplicationRound::query()
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();
    }
}
