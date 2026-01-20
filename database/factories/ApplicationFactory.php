<?php

namespace Database\Factories;

use App\Enums\ApplicationCategory;
use App\Enums\ApplicationStatus;
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
            'category' => $this->faker->randomElement(ApplicationCategory::cases()),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(ApplicationStatus::cases()),
        ];
    }
}
