<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseReport;
use App\Models\CommentReport;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseComment;
use Faker\Factory as Faker;

class ReportSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            CourseReport::create([
                'user_id' => User::inRandomOrder()->first()->id,
                'course_id' => Course::inRandomOrder()->first()->id,
                'content' => $faker->paragraph,
            ]);
        }

        for ($i = 0; $i < 20; $i++) {
            CommentReport::create([
                'user_id' => User::inRandomOrder()->first()->id,
                'comment_id' => CourseComment::inRandomOrder()->first()->id,
                'content' => $faker->paragraph,
            ]);
        }
    }
}
