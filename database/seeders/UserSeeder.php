<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Domain;
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

        // BANGKHEN
        //Admin
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
        ]);

        User::factory()->count(10)->create([
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

        User::factory()->create([
            'name' => 'Farhana Malem',
            'email' => 'hana@example.com',
            'role' => UserRole::STUDENT,
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
        ]);

        User::factory()->create([
            'name' => 'Dr. Somchai Prasert',
            'email' => 'hod1@example.com',
            'role' => UserRole::COMMITTEE,
            'position' => 'Head of Department',
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
            'domain' => Domain::BANGKHEN,
        ]);

        User::factory()->create([
            'name' => 'Assoc. Prof. Suda Kittipong',
            'email' => 'assocdean1@example.com',
            'role' => UserRole::COMMITTEE,
            'position' => 'Associate Dean',
            'faculty' => Faculty::SCIENCE,
            'domain' => Domain::BANGKHEN,
        ]);

        User::factory()->create([
            'name' => 'Prof. Chaiwat Rattanakul',
            'email' => 'dean1@example.com',
            'role' => UserRole::COMMITTEE,
            'position' => 'Dean',
            'faculty' => Faculty::SCIENCE,
            'domain' => Domain::BANGKHEN,
        ]);

        User::factory()->create([
            'name' => 'Dr. Preecha Boonmee',
            'email' => 'committee1@example.com',
            'role' => UserRole::COMMITTEE,
            'position' => 'Committee Member',
            'domain' => Domain::BANGKHEN,
        ]);



        User::factory()->count(5)->withImage()->create();
        User::factory()->count(44)->create();
        //Committee
        User::factory()->count(10)->committee(Domain::BANGKHEN, 'Head of Department')->create();
        User::factory()->count(5)->committee(Domain::BANGKHEN, 'Associate Dean')->create();
        User::factory()->count(2)->committee(Domain::BANGKHEN, 'Dean')->create();
        User::factory()->count(15)->committee(Domain::BANGKHEN, 'Committee Member')->create();

        //KAMPHAENG SEAN
        //Admin
        User::factory()->create([
            'name' => 'Kamp Phaeng',
            'email' => 'sean@example.com',
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
            'domain' => Domain::KAMPHAENG_SEAN,
        ]);

        User::factory()->count(20)->create([
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'domain' => Domain::KAMPHAENG_SEAN,
        ]);
        // Student


        User::factory()->count(50)->create([
            'domain' => Domain::KAMPHAENG_SEAN,
        ]);
        //Committee
        User::factory()->count(10)->committee(Domain::KAMPHAENG_SEAN, 'Head of Department')->create();
        User::factory()->count(5)->committee(Domain::KAMPHAENG_SEAN, 'Associate Dean')->create();
        User::factory()->count(2)->committee(Domain::KAMPHAENG_SEAN, 'Dean')->create();
        User::factory()->count(15)->committee(Domain::KAMPHAENG_SEAN, 'Committee Member')->create();

        //SRIRACHA
        //Admin
        User::factory()->create([
            'name' => 'Sri Racha',
            'email' => 'racha@example.com',
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
            'domain' => Domain::SRIRACHA,
        ]);

        User::factory()->count(20)->create([
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'domain' => Domain::SRIRACHA,
        ]);
        // Student


        User::factory()->count(50)->create([
            'domain' => Domain::SRIRACHA,
        ]);
        //Committee
        User::factory()->count(10)->committee(Domain::SRIRACHA, 'Head of Department')->create();
        User::factory()->count(5)->committee(Domain::SRIRACHA, 'Associate Dean')->create();
        User::factory()->count(2)->committee(Domain::SRIRACHA, 'Dean')->create();
        User::factory()->count(15)->committee(Domain::SRIRACHA, 'Committee Member')->create();

        //CHALERMPHRAKIAT
        //Admin
        User::factory()->create([
            'name' => 'CHALERM PHRAKIAT',
            'email' => 'chal@example.com',
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
            'domain' => Domain::CHALERMPHRAKIAT,
        ]);

        User::factory()->count(20)->create([
            'role' => UserRole::ADMIN,
            'position' => 'Student Development Division',
            'domain' => Domain::CHALERMPHRAKIAT,
        ]);
        // Student


        User::factory()->count(50)->create([
            'domain' => Domain::CHALERMPHRAKIAT,
        ]);
        //Committee
        User::factory()->count(10)->committee(Domain::CHALERMPHRAKIAT, 'Head of Department')->create();
        User::factory()->count(5)->committee(Domain::CHALERMPHRAKIAT, 'Associate Dean')->create();
        User::factory()->count(2)->committee(Domain::CHALERMPHRAKIAT, 'Dean')->create();
        User::factory()->count(15)->committee(Domain::CHALERMPHRAKIAT, 'Committee Member')->create();
    }
}
