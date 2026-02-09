<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ApplicationRoundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year' => $this->faker->unique()->numberBetween(2020, 2030),
            'semester' => $this->faker->randomElement(\App\Enums\Semester::cases()),
            'start_time' => now(),
            'end_time' => now()->addMonth(),
            'status' => \App\Enums\RoundStatus::CLOSED,
        ];
    }
}
