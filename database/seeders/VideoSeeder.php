<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\Course;
use Faker\Factory as Faker;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $courses = Course::all();

        $youtubeLinks = [
            'https://youtu.be/4RhY1JJgLsM?si=Nqe19pxP2p5fe66q',
            'https://youtu.be/VnU6KFDGm-w?si=vNS-J5FxjNIsYMIN',
            'https://youtu.be/CUbvMoOvrYY?si=v-JkJcJvc1FU85hG',
            'https://youtu.be/3Jdy9rfYqN0?si=X4q26oupm-rcVBBe',
            'https://youtu.be/e5QcI5mDUBI?si=8C3tlH-ztq-N_pYI'
        ];

        $youtubeTitle = [
          'Laravel 1',
          'Laravel 2',
          'Laravel 3',
          'Laravel 4',
          'Laravel 5',
        ];

        foreach ($courses as $course) {
            foreach (range(1, 5) as $index) {
                Video::create([
                    'course_id' => $course->id,
                    'title' => $faker->randomElement($youtubeTitle),
                    'path' => $faker->randomElement($youtubeLinks),
                ]);
            }
        }
    }
}
