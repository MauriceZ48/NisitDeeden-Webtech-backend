<?php

namespace App\Repositories;


use App\Enums\RoundStatus;
use App\Models\ApplicationRound;
use App\Repositories\Traits\SimpleCRUD;

class ApplicationRoundRepository{
    use SimpleCRUD;

    protected $model;

    public function __construct(ApplicationRound $model) {
        $this->model = $model;
    }
    public function getAllOrdered()
    {
        return ApplicationRound::withCount('applications')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
    }

    public function getActive()
    {
        return ApplicationRound::active()->first();
    }

    public function anotherRoundIsActive($excludeId = null): bool
    {
        return ApplicationRound::active()
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    public function isOverlapping($startTime, $endTime, $excludeId = null): bool
    {
        return ApplicationRound::query()
            //Exclude the current round being edited so it doesn't collide with itself
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();
    }

    public function getNextExpectedRound(): array
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
}
