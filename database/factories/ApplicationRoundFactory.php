<?php

namespace Database\Factories;

use App\Enums\Domain;
use App\Enums\RoundStatus;
use App\Enums\Semester;
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
            'academic_year' => $this->faker->numberBetween(2020, 2030),
            'semester' => $this->faker->randomElement(Semester::cases()),
            'start_time' => now(),
            'end_time' => now()->addMonth(),
            'status' => RoundStatus::CLOSED,
            'domain' => Domain::BANGKHEN,
        ];
    }
}
