<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationAttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationAttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get all applications with their category attributes
        $applications = Application::with(['applicationCategory.attributes'])->get();

        foreach ($applications as $app) {
            $attributes = $app->applicationCategory->attributes;

            foreach ($attributes as $attribute) {
                $mockValue = '';

                // 2. Generate mock data based on your 3 specific types
                if ($attribute->type === 'text') {
                    $mockValue = fake()->sentence();
                } elseif ($attribute->type === 'textarea') {
                    $mockValue = fake()->paragraph();
                } elseif ($attribute->type === 'file') {
                    $mockValue = 'applications/dynamic_submissions/mock_file.pdf';
                } else {
                    $mockValue = 'Mock data for ' . $attribute->label;
                }

                // 3. Create the record
                ApplicationAttributeValue::create([
                    'application_id' => $app->id,
                    'category_attribute_id' => $attribute->id,
                    'value' => $mockValue ?? '',
                ]);
            }
        }
    }
}
