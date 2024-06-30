<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $users = User::all();
        $categories = Category::all();

        $courseTitle = [
          'How to Learn Programming in 30 Days',
            'Top 10 Travel Destinations for 2024',
            'The Ultimate Guide to Cooking Pasta',
            '5 Tips for a Successful Job Interview',
            'Exploring the Beauty of National Parks'
        ];

        foreach (range(1, 50) as $index) {
            Course::create([
                'creator_id' => $faker->numberBetween(4, 9),
                'category_id' => $faker->numberBetween(1, 8),
                'title' => $faker->randomElement($courseTitle),
                'image' => $faker->imageUrl(640, 480, 'education', true, 'Faker'),
                'description' => $faker->paragraphs(3, true),
                'cost' => $faker->randomFloat(2, 0, 100),
                'average_rating' => $faker->numberBetween(1, 5),
                'is_reviewed' => $faker->boolean,
            ]);
        }
    }
}
