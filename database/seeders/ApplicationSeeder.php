<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\ApplicationCategory;
use App\Models\ApplicationRound;
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
        $rounds = ApplicationRound::all();

        if ($rounds->isEmpty()) {
            $this->command->warn("No Application Rounds found. Seed rounds first!");
            return;
        }

        foreach ($students as $student) {
            // Only pick rounds that belong to the SAME domain as the student
            $matchingRounds = $rounds->where('domain', $student->domain);
            $matchingCategories = ApplicationCategory::where('domain', $student->domain)->get();

            if ($matchingRounds->isEmpty() || $matchingCategories->isEmpty()){
                continue;
            }

            $selectedRounds = $matchingRounds->random(min(2, $matchingRounds->count()));

            foreach ($selectedRounds as $round) {
                $status = fake()->randomElement(ApplicationStatus::cases());
                $domain = $round->domain;
                $category = $matchingCategories->random();

                Application::factory()->create([
                    'user_id' => $student->id,
                    'application_round_id' => $round->id,
                    'application_category_id' => $category->id,
                    'status' => $status,
                    'rejection_reason' => ($status === ApplicationStatus::REJECTED) ? fake()->sentence() : null,
                    'domain' => $domain,
                ]);
            }
        }
    }
}
