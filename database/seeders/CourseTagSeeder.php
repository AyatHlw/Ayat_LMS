<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();
        $tags = Tag::all();

        foreach ($courses as $course) {
            // Attach random tags to each course
            $course->tags()->attach($tags->random(rand(1, 3))->pluck('id')->toArray());
        }
    }
}
