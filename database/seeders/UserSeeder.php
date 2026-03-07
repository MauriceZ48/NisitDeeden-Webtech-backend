<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Domain;
use App\Enums\Faculty;
use App\Enums\UserPosition;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. สร้างผู้ใช้ที่ระบุชื่อเฉพาะเจาะจงก่อน (Admin/Student)
        $this->seedSpecificUsers();

        // 2. รันระบบสร้างข้อมูลตามลำดับขั้นสำหรับทุกวิทยาเขต
        foreach (Domain::cases() as $domain) {
            $this->seedDomainHierarchy($domain);
        }
    }

    private function seedDomainHierarchy(Domain $domain): void
    {
        // --- ระดับวิทยาเขต (ส่วนกลาง) ---

        // กองพัฒนานิสิตประจำวิทยาเขต (ไม่มีคณะ, ไม่มีภาควิชา)
        User::factory()->count(10)->create([
            'role' => UserRole::ADMIN,
            'position' => UserPosition::STAFF,
            'domain' => $domain,
            'faculty' => null,
            'department' => null,
        ]);

        // กรรมการกลางประจำวิทยาเขต (ไม่มีคณะ, ไม่มีภาควิชา)
        User::factory()->count(10)->create([
            'role' => UserRole::COMMITTEE,
            'position' => UserPosition::COMMITTEE_MEMBER,
            'domain' => $domain,
            'faculty' => null,
            'department' => null,
        ]);

        foreach (Faculty::cases() as $faculty) {

            // --- ระดับคณะ ---

            // 1. คณบดี (ระบุชื่อเฉพาะสำหรับคณะวิทยาศาสตร์ บางเขน)
            if ($domain === Domain::BANGKHEN && $faculty === Faculty::SCIENCE) {
                User::factory()->create([
                    'name' => 'ศ.ดร. มานะ อดทน',
                    'email' => 'sci_dean@ku.ac.th',
                    'role' => UserRole::COMMITTEE,
                    'position' => UserPosition::DEAN,
                    'faculty' => $faculty,
                    'department' => null,
                    'domain' => $domain,
                ]);
            } else {
                User::factory()->create([
                    'role' => UserRole::COMMITTEE,
                    'position' => UserPosition::DEAN,
                    'faculty' => $faculty,
                    'department' => null,
                    'domain' => $domain,
                ]);
            }

            // 2. รองคณบดี (สุ่ม 2-4 คนต่อคณะ)
            if ($domain === Domain::BANGKHEN && $faculty === Faculty::SCIENCE) {
                User::factory()->create([
                    'name' => 'รองศาสตราจารย์ ดร. สมชาย ใจดี',
                    'email' => 'assoc_dean_sci@ku.ac.th',
                    'role' => UserRole::COMMITTEE,
                    'position' => UserPosition::ASSOCIATE_DEAN,
                    'faculty' => $faculty,
                    'department' => null,
                    'domain' => $domain,
                ]);
            }

            User::factory()->count(rand(2, 4))->create([
                'role' => UserRole::COMMITTEE,
                'position' => UserPosition::ASSOCIATE_DEAN,
                'faculty' => $faculty,
                'department' => null,
                'domain' => $domain,
            ]);

            // --- ระดับภาควิชา ---
            $departments = $this->getDepartmentsByFaculty($faculty);

            foreach ($departments as $dept) {

                // 1. หัวหน้าภาควิชา
                if ($domain === Domain::BANGKHEN && $dept === Department::COMPUTER) {
                    User::factory()->create([
                        'name' => 'ผศ.ดร. นารี รักเรียน',
                        'email' => 'comsci_head@ku.ac.th',
                        'role' => UserRole::COMMITTEE,
                        'position' => UserPosition::HEAD_OF_DEPARTMENT,
                        'faculty' => $faculty,
                        'department' => $dept,
                        'domain' => $domain,
                    ]);
                } else {
                    User::factory()->create([
                        'role' => UserRole::COMMITTEE,
                        'position' => UserPosition::HEAD_OF_DEPARTMENT,
                        'faculty' => $faculty,
                        'department' => $dept,
                        'domain' => $domain,
                    ]);
                }

                // 2. นิสิต (ต้องมีทั้งคณะและภาควิชา)
                User::factory()->count(rand(5, 10))->create([
                    'role' => UserRole::STUDENT,
                    'position' => UserPosition::STUDENT,
                    'faculty' => $faculty,
                    'department' => $dept,
                    'domain' => $domain,
                ]);
            }
        }
    }

    private function seedSpecificUsers(): void
    {
        // ข้อมูลเฉพาะสำหรับบางเขน
        User::factory()->create([
            'name' => 'ผู้ดูแลระบบ กองพัฒนานิสิต',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
            'position' => UserPosition::STAFF,
            'domain' => Domain::BANGKHEN,
            'faculty' => null,
            'department' => null,
        ]);

        User::factory()->create([
            'name' => 'สแตมป์ พิชา',
            'email' => 'stamp@example.com',
            'role' => UserRole::STUDENT,
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
            'domain' => Domain::BANGKHEN,
        ]);

        User::factory()->create([
            'name' => 'ฟาร์ฮาน่า มาเล็ม',
            'email' => 'hana@example.com',
            'role' => UserRole::STUDENT,
            'faculty' => Faculty::SCIENCE,
            'department' => Department::COMPUTER,
            'domain' => Domain::BANGKHEN,
        ]);
<<<<<<< HEAD

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
=======
>>>>>>> de3fd6e287f4008d19f1351d9a4ba551b19c2978

        // แอดมินกองพัฒนานิสิตวิทยาเขตอื่นๆ
        User::factory()->create([
            'name' => 'กองพัฒนานิสิต วิทยาเขตกำแพงแสน',
            'email' => 'sean@example.com',
            'role' => UserRole::ADMIN,
            'position' => UserPosition::STAFF,
            'domain' => Domain::KAMPHAENG_SEAN,
            'faculty' => null,
            'department' => null,
        ]);

<<<<<<< HEAD
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
=======
>>>>>>> de3fd6e287f4008d19f1351d9a4ba551b19c2978
        User::factory()->create([
            'name' => 'กองพัฒนานิสิต วิทยาเขตศรีราชา',
            'email' => 'racha@example.com',
            'role' => UserRole::ADMIN,
            'position' => UserPosition::STAFF,
            'domain' => Domain::SRIRACHA,
            'faculty' => null,
            'department' => null,
        ]);

<<<<<<< HEAD
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
=======
>>>>>>> de3fd6e287f4008d19f1351d9a4ba551b19c2978
        User::factory()->create([
            'name' => 'กองพัฒนานิสิต วิทยาเขตเฉลิมพระเกียรติ',
            'email' => 'chal@example.com',
            'role' => UserRole::ADMIN,
            'position' => UserPosition::STAFF,
            'domain' => Domain::CHALERMPHRAKIAT,
            'faculty' => null,
            'department' => null,
        ]);
    }

<<<<<<< HEAD
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
=======
    private function getDepartmentsByFaculty(Faculty $faculty): array
    {
        return array_filter(Department::cases(), fn($dept) => $dept->faculty() === $faculty);
>>>>>>> de3fd6e287f4008d19f1351d9a4ba551b19c2978
    }
}
