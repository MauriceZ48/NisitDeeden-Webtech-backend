<?php

namespace Database\Factories;

use App\Models\ApplicationCategory;
use App\Enums\ApplicationStatus;
use App\Models\ApplicationRound;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'application_round_id' => ApplicationRound::inRandomOrder()->first()
                ?? ApplicationRound::factory(),
            'application_category_id' => ApplicationCategory::inRandomOrder()->first()
                ?? ApplicationCategory::factory(),
            'status' => fake()->randomElement(ApplicationStatus::cases()),
            'rejection_reason' => null,
        ];
    }
}
