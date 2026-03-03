<?php

namespace Database\Seeders;

use App\Enums\Domain;
use App\Models\ApplicationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Activity',
                'description' => 'Leadership, community service, and extracurricular activities.',
                'icon' => 'lucide:award',
            ],
            [
                'name' => 'Creativity',
                'description' => 'Arts, innovation, and creative problem-solving.',
                'icon' => 'lucide:lightbulb',
            ],
            [
                'name' => 'Behavior',
                'description' => 'Ethics, discipline, and outstanding conduct.',
                'icon' => 'lucide:shield-check',
            ],
        ];

        // Loop through each campus
        foreach (Domain::cases() as $domain) {
            foreach ($categories as $category) {
                ApplicationCategory::create([
                    'name'        => $category['name'],
                    'slug'        => Str::slug($category['name'] . '-' . $domain->value),
                    'description' => $category['description'],
                    'icon'        => $category['icon'],
                    'is_active'   => true,
                    'domain'      => $domain, // Use the Enum object directly
                ]);
            }
        }
    }
}
