<?php

namespace Database\Seeders;

use App\Models\ApplicationCategory;
use App\Models\CategoryAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{

    public function run(): void
    {
        $activity = ApplicationCategory::where('slug', 'activity')->first();
        $creativity = ApplicationCategory::where('slug', 'creativity')->first();
        $behavior =  ApplicationCategory::where('slug', 'behavior')->first();

        // Activity
        $this->createAttributes($activity->id, [
            ['label' => 'Organization Name', 'type' => 'text', 'is_required' => true], // Added here!
            ['label' => 'Co-curricular Activity Transcript', 'type' => 'file', 'is_required' => true],
            ['label' => 'Project Documents', 'type' => 'file', 'is_required' => true],
            ['label' => 'Activity Photos', 'type' => 'file', 'is_required' => true],
        ]);

        // Creativity
        $this->createAttributes($creativity->id, [
            ['label' => 'Project/Innovation Name', 'type' => 'text', 'is_required' => true], // Or here!
            ['label' => 'Evidence of Award (Certificate/Results)', 'type' => 'file', 'is_required' => true],
            ['label' => 'Project Portfolio ', 'type' => 'textarea', 'is_required' => true],
        ]);

        // Behavior
        $this->createAttributes($behavior->id, [
            ['label' => 'Behavior Certificate Title', 'type' => 'text', 'is_required' => false], // Optional
            ['label' => 'Teacher Recommendation Form (Signed PDF)', 'type' => 'file', 'is_required' => true],
        ]);
    }

    /**
     * Helper function to keep the run() method clean
     */
    private function createAttributes(int $categoryId, array $fields): void
    {
        foreach ($fields as $field) {

            $dataToSave = array_merge($field, [
                'application_category_id' => $categoryId
            ]);

            CategoryAttribute::create($dataToSave);
        }
    }
}
