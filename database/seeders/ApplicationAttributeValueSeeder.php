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
        // 1. Get all applications
        $applications = Application::with(['applicationCategory.attributes', 'user'])->get();

        foreach ($applications as $app) {
            // 2. Find which attributes belong to this application's category
            $attributes = $app->applicationCategory->attributes;

            foreach ($attributes as $attribute) {
                // 3. Create a mock value based on the type
                if ($attribute->type === 'number') {
                    $mockValue = rand(1, 100);
                } elseif ($attribute->type === 'text') {
                    $mockValue = fake()->sentence();
                } elseif ($attribute->type === 'file' || $attribute->type === 'image') {
                    $mockValue = 'mocks/sample-file.pdf';
                } elseif ($attribute->type === 'url') {
                    $username = Str::slug($app->user->name);
                    $mockValue = 'https://github.com/' . $username . '/nisit-deeden';
                } else {
                    $mockValue = 'Mock data for ' . $attribute->label;
                }

                ApplicationAttributeValue::create([
                    'application_id' => $app->id,
                    'category_attribute_id' => $attribute->id,
                    'value' => $mockValue,
                ]);
            }
        }
    }
}
