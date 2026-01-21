<?php

namespace Database\Factories;

use App\Enums\Faculty;
use App\Enums\Department;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $department = fake()->randomElement(Department::cases());

        $faculty = $department->faculty();

        return [
            'profile_path' => null,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'university_id' => fake()->unique()->bothify('ID-#####'),
            'department' => $department,
            'faculty' => $faculty,
            'role' => UserRole::USER,
        ];
    }

    public function withImage(): static
    {
        return $this->state(function (array $attributes) {
            $name = $attributes['name'] ?? 'user';

            // 1. Generate a filename
            $filename = 'profile-photos/seed-' . Str::random(10) . '.jpg';

            // 2. Get a random high-quality image from a service like Unsplash
            // We use @ to suppress errors in case of no internet connection
            $image = @file_get_contents("https://xsgames.co/randomusers/avatar.php?g=pixel");

            if ($image) {
                // 3. Save it to your local storage
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $image);
                return ['profile_path' => $filename];
            }

            return ['profile_path' => null];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
