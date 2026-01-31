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

        $rounds = ApplicationRound::orderBy('academic_year', 'desc')
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

        // 1. SEQUENTIAL GUARD: Is this the correct next Year/Semester?
        $expected = $this->getNextExpectedRound();
        if ($request->academic_year != $expected['year'] ||
            $request->semester != $expected['semester']->value) {
            return back()->withErrors([
                'academic_year' => "Next round must be {$expected['year']} Semester {$expected['semester']->value}."
            ]);
        }

        // 2. CONCURRENCY GUARD: If opening this new round, is another one already open?
        if ($request->status === RoundStatus::OPEN->value && $this->anotherRoundIsActive()) {
            return back()->withErrors([
                'status' => 'Cannot create an open round while another is already active.'
            ]);
        }

        // 3. TIME GUARD: Is the end_time actually in the future?
        if ($request->status === RoundStatus::OPEN->value && $request->date('end_time')->isPast()) {
            return back()->withErrors([
                'end_time' => 'To open this round, the end time must be in the future.'
            ]);
        }

        // 4. OVERLAP GUARD: Ensure these dates don't clash with others
        if ($this->isOverlapping($request->start_time, $request->end_time)) {
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApplicationRoundRequest $request, ApplicationRound $applicationRound)
    {

        if ($request->status === RoundStatus::OPEN->value) {

            // A. REVISED SECURITY: Check if a NEWER round has already started
            $newerRoundStarted = ApplicationRound::where('id', '!=', $applicationRound->id)
                ->where('start_time', '>', $applicationRound->end_time)
                ->where('status', RoundStatus::OPEN->value)
                ->exists();

            if ($newerRoundStarted) {
                return back()->withErrors(['status' => 'You cannot reopen this round because a newer round has already started.']);
            }

            // B. EMERGENCY: Check if the NEWLY provided date is in the future
            $newEndTime = $request->date('end_time');
            if ($newEndTime->isPast()) {
                return back()->withErrors(['end_time' => 'To open this round, the end time must be set to a future date.']);
            }

            // C. CONCURRENCY: Check if another round is active
            if ($this->anotherRoundIsActive($applicationRound->id)) {
                return back()->withErrors(['status' => 'Another round is already open. Close it before opening this one.']);
            }
        }

        // D. OVERLAP: Always check this, pass the ID to avoid self-collision
        if ($this->isOverlapping($request->date('start_time'), $request->date('end_time'), $applicationRound->id)) {
            return back()->withErrors(['start_time' => 'These dates overlap with another existing round.'])->withInput();
        }

        $applicationRound->update($request->validated());
        return redirect()->route('rounds.index')->with('success', 'Round updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicationRound $applicationRound)
    {
        //
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

        // In your getNextExpectedRound helper
        if (!$lastRound) {
            // Return the Enum case itself, not a string
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
