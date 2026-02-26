<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
// Update ApplicationSeeder.php
    public function run(): void
    {
        $students = User::query()->where('role', UserRole::STUDENT)->get();
        $rounds = \App\Models\ApplicationRound::all();

        if ($rounds->isEmpty()) {
            $this->command->warn("No Application Rounds found. Seed rounds first!");
            return;
        }

        foreach ($students as $student) {
            $selectedRounds = $rounds->random(min(3, $rounds->count()));

            foreach ($selectedRounds as $round) {
                // 1. Randomly pick a status from your Enum cases
                $status = fake()->randomElement(\App\Enums\ApplicationStatus::cases());

                // 2. Logic for rejection reason
                $rejectReason = null;
                if ($status === \App\Enums\ApplicationStatus::REJECTED) {
                    $rejectReason = fake()->sentence(); // Generates a random reason
                }

                Application::factory()->create([
                    'user_id' => $student->id,
                    'application_round_id' => $round->id,
                    'status' => $status,
                    'rejection_reason' => $rejectReason,
                ]);
            }
        }
    }
}
