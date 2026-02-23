<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Faculty;
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
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
        ]);

        User::factory()->create([
            'name' => 'Stamp Picha',
            'email' => 'stamp@exmple.com',
            'role' => UserRole::USER,
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
        ]);

        User::factory()->count(5)->withImage()->create();

        User::factory()->count(44)->create();

    }
}
