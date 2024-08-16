<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseComment;
use App\Models\CourseReport;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 20; $i++) {
            CourseComment::create([
                'user_id' => User::query()->inRandomOrder()->first()->id,
                'course_id' => Course::query()->inRandomOrder()->first()->id,
                'rating' => $faker->numberBetween(1, 5),
                'content' => $faker->paragraph,
            ]);
        }
    }
}
