<?php

namespace Database\Seeders;

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
                'icon' => 'activity',
            ],
            [
                'name' => 'Creativity',
                'description' => 'Arts, innovation, and creative problem-solving.',
                'icon' => 'creativity',
            ],
            [
                'name' => 'Behavior',
                'description' => 'Ethics, discipline, and outstanding conduct.',
                'icon' => 'behavior',
            ],
        ];

        foreach ($categories as $category) {
            ApplicationCategory::create([
                'name'  => $category['name'],
                'slug'  => Str::slug($category['name']),
                'description' => $category['description'],
                'icon'  => $category['icon'],
                'is_active' => true,
            ]);
        }

    }
}
