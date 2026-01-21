<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Attachment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = Application::all();

        foreach ($applications as $app) {
            Attachment::factory(rand(1, 3))->create([
                'application_id' => $app->id
            ]);
        }
    }
}
