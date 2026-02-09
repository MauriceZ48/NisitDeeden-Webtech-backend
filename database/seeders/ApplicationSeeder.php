<?php

namespace Database\Seeders;

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
        $users = User::all();
        $rounds = \App\Models\ApplicationRound::all();

        foreach ($users as $user) {
            // Take 2 random but UNIQUE rounds from your seeded rounds
            $selectedRounds = $rounds->random(min(2, $rounds->count()));

            foreach ($selectedRounds as $round) {
                Application::factory()->create([
                    'user_id' => $user->id,
                    'application_round_id' => $round->id,
                ]);
            }
        }
    }
}
