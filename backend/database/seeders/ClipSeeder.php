<?php

namespace Database\Seeders;

use App\Models\Clip;
use Illuminate\Database\Seeder;

class ClipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clip::factory()->count(8)->create();

        Clip::factory()->create([
            'title' => 'Laravel Queue Basics',
            'description' => 'Introduction to queue workers and jobs.',
            'url' => 'https://example.com/videos/laravel-queue-basics',
            'status' => 'active',
        ]);

        Clip::factory()->create([
            'title' => 'Vue Composition API Deep Dive',
            'description' => 'Reactive state patterns with composables.',
            'url' => 'https://example.com/videos/vue-composition-api',
            'status' => 'inactive',
        ]);
    }
}
