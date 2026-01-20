<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        User::factory()->count(49)->create();

    }
}
