<?php

namespace Database\Seeders;

use App\Models\ApplicationCategory;
use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Get ALL categories across ALL campuses
        $allCategories = ApplicationCategory::all();

        foreach ($allCategories as $category) {
            // Check the name and assign the correct attributes
            if (str_contains($category->name, 'Activity')) {
                $this->createAttributes($category->id, [
                    ['label' => 'Organization Name', 'type' => 'text', 'is_required' => true],
                    ['label' => 'Co-curricular Activity Transcript', 'type' => 'file', 'is_required' => true],
                    ['label' => 'Project Documents', 'type' => 'file', 'is_required' => true],
                    ['label' => 'Activity Photos', 'type' => 'file', 'is_required' => true],
                ]);
            }

            if (str_contains($category->name, 'Creativity')) {
                $this->createAttributes($category->id, [
                    ['label' => 'Project/Innovation Name', 'type' => 'text', 'is_required' => true],
                    ['label' => 'Evidence of Award (Certificate/Results)', 'type' => 'file', 'is_required' => true],
                    ['label' => 'Project Portfolio', 'type' => 'textarea', 'is_required' => true],
                ]);
            }

            if (str_contains($category->name, 'Behavior')) {
                $this->createAttributes($category->id, [
                    ['label' => 'Behavior Certificate Title', 'type' => 'text', 'is_required' => false],
                    ['label' => 'Teacher Recommendation Form (Signed PDF)', 'type' => 'file', 'is_required' => true],
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
