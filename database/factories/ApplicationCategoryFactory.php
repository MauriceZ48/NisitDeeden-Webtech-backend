<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationCategory>
 */
class ApplicationCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>'รางวัลด้าน ' . $this->faker->unique()->word(),
            'description' => $this->faker->text(),
            'icon' => 'category_icons',
            'is_active' => $this->faker->boolean(),
        ];
    }
}
