<?php

namespace Database\Seeders;

use App\Models\ApplicationCategory;
use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $allCategories = ApplicationCategory::all();

        foreach ($allCategories as $category) {
            if (str_contains($category->name, 'กิจกรรม')) {
                $this->createAttributes($category->id, [
                    ['label' => 'ชื่อองค์กร / ชมรม / สโมสร', 'type' => 'text', 'is_required' => true],
                    ['label' => 'ทรานสคริปต์กิจกรรมเสริมหลักสูตร (PDF)', 'type' => 'file', 'is_required' => true],
                    ['label' => 'เอกสารสรุปโครงการที่เข้าร่วม', 'type' => 'file', 'is_required' => true],
                    ['label' => 'ภาพถ่ายขณะปฏิบัติกิจกรรม', 'type' => 'file', 'is_required' => true],
                ]);
            }

            if (str_contains($category->name, 'ความคิดสร้างสรรค์')) {
                $this->createAttributes($category->id, [
                    ['label' => 'ชื่อโครงการ / นวัตกรรม / ผลงาน', 'type' => 'text', 'is_required' => true],
                    ['label' => 'หลักฐานการได้รับรางวัล (เกียรติบัตร / ผลการตัดสิน)', 'type' => 'file', 'is_required' => true],
                    ['label' => 'แฟ้มสะสมผลงาน (Portfolio)', 'type' => 'textarea', 'is_required' => true],
                ]);
            }

            if (str_contains($category->name, 'ความประพฤติ')) {
                $this->createAttributes($category->id, [
                    ['label' => 'ชื่อรางวัลด้านความประพฤติที่เคยได้รับ (ถ้ามี)', 'type' => 'text', 'is_required' => false],
                    ['label' => 'หนังสือรับรองจากอาจารย์ที่ปรึกษา (PDF พร้อมลายเซ็น)', 'type' => 'file', 'is_required' => true],
                ]);
            }
        }
    }

    private function createAttributes(int $categoryId, array $fields): void
    {
        foreach ($fields as $field) {
            CategoryAttribute::create(array_merge($field, [
                'application_category_id' => $categoryId
            ]));
        }
    }
}
