<?php

namespace Database\Seeders;

use App\Enums\Domain;
use App\Enums\RoundStatus;
use App\Enums\Semester;
use App\Models\ApplicationRound;
use Illuminate\Database\Seeder;

class ApplicationRoundSeeder extends Seeder
{
    public function run(): void
    {
        // Loop through every campus defined in your Enum
        foreach (Domain::cases() as $domain) {

            if ($domain !== Domain::ALL) {
                // --- 2023 ACADEMIC YEAR ---
                ApplicationRound::factory()->create([
                    'academic_year' => '2023',
                    'semester' => Semester::FIRST,
                    'start_time' => '2023-08-01 09:00:00',
                    'end_time' => '2023-08-31 16:00:00',
                    'status' => RoundStatus::CLOSED,
                    'domain' => $domain,
                ]);

                ApplicationRound::factory()->create([
                    'academic_year' => '2023',
                    'semester' => Semester::SECOND,
                    'start_time' => '2024-01-15 09:00:00',
                    'end_time' => '2024-02-15 16:00:00',
                    'status' => RoundStatus::CLOSED,
                    'domain' => $domain,
                ]);

                // --- 2024 ACADEMIC YEAR ---
                ApplicationRound::factory()->create([
                    'academic_year' => '2024',
                    'semester' => Semester::FIRST,
                    'start_time' => '2024-08-01 09:00:00',
                    'end_time' => '2024-08-31 16:00:00',
                    'status' => RoundStatus::CLOSED,
                    'domain' => $domain,
                ]);

                ApplicationRound::factory()->create([
                    'academic_year' => '2024',
                    'semester' => Semester::SECOND,
                    'start_time' => '2025-01-15 09:00:00',
                    'end_time' => '2025-02-15 16:00:00',
                    'status' => RoundStatus::CLOSED,
                    'domain' => $domain,
                ]);

                // --- 2025 ACADEMIC YEAR ---
                ApplicationRound::factory()->create([
                    'academic_year' => '2025',
                    'semester' => Semester::FIRST,
                    'start_time' => '2025-08-01 09:00:00',
                    'end_time' => '2025-08-31 16:00:00',
                    'status' => RoundStatus::CLOSED,
                    'domain' => $domain,
                ]);

                // Current Round: Semester 2 (OPEN)
                ApplicationRound::factory()->create([
                    'academic_year' => '2025',
                    'semester' => Semester::SECOND,
                    'start_time' => now()->subDays(5),
                    'end_time' => now()->addDays(25),
                    'status' => RoundStatus::OPEN,
                    'domain' => $domain,
                ]);
            }

        }
    }
}
