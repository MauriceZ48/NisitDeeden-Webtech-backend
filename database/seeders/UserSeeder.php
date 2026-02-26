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
        //Admin
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
        ]);

        User::factory()->count(20)->create([
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
        ]);

        //Student
        User::factory()->create([
            'name' => 'Stamp Picha',
            'email' => 'stamp@example.com',
            'role' => UserRole::STUDENT,
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
        ]);
        User::factory()->count(5)->withImage()->create();
        User::factory()->count(44)->create();
        //Committee
        User::factory()->count(10)->committee('Head of Department')->create();
        User::factory()->count(5)->committee('Associate Dean')->create();
        User::factory()->count(2)->committee('Dean')->create();
        User::factory()->count(15)->committee('Committee Member')->create();

    }
}
