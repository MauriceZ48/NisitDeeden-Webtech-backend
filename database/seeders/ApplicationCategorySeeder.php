<?php

namespace Database\Seeders;

use App\Enums\Domain;
use App\Models\ApplicationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'ด้านกิจกรรมนิสิต',
                'description' => 'ความเป็นผู้นำ การบำเพ็ญประโยชน์ และการมีส่วนร่วมในกิจกรรม',
                'icon' => 'lucide:award',
            ],
            [
                'name' => 'ด้านความคิดสร้างสรรค์',
                'description' => 'ผลงานศิลปะ นวัตกรรม และการแก้ปัญหาอย่างสร้างสรรค์',
                'icon' => 'lucide:lightbulb',
            ],
            [
                'name' => 'ด้านความประพฤติ',
                'description' => 'จริยธรรม ระเบียบวินัย และการเป็นแบบอย่างที่ดีเด่น',
                'icon' => 'lucide:shield-check',
            ],
        ];

        foreach (Domain::cases() as $domain) {
            if ($domain !== Domain::ALL) {
                foreach ($categories as $category) {
                    ApplicationCategory::create([
                        'name'        => $category['name'],
                        'description' => $category['description'],
                        'icon'        => $category['icon'],
                        'is_active'   => true,
                        'domain'      => $domain,
                    ]);
                }
            }
        }

        ApplicationCategory::create([
            'name'        => 'รางวัลนิสิตพระราชทาน (ระดับมหาวิทยาลัย)',
            'description' => 'รางวัลเกียรติยศสูงสุดสำหรับนิสิต',
            'icon'        => 'lucide:crown',
            'is_active'   => true,
            'domain'      => Domain::ALL,
        ]);
    }
}
