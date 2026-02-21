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
                'icon' => 'category_icons/activity.png',
            ],
            [
                'name' => 'Creativity',
                'description' => 'Arts, innovation, and creative problem-solving.',
                'icon' => 'category_icons/creativity.png',
            ],
            [
                'name' => 'Behavior',
                'description' => 'Ethics, discipline, and outstanding conduct.',
                'icon' => 'category_icons/behavior.png',
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
