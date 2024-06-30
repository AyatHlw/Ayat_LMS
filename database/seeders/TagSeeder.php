<?php

namespace Database\Seeders;

use App\Models\Tag;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $tags = Tag::all();

        $taggs = [
            'Programming', 'Web Development', 'Data Science', 'Machine Learning',
            'Artificial Intelligence', 'Cloud Computing', 'Cyber Security', 'Blockchain'
        ];

        foreach ($taggs as $tag) {
            Tag::create([
                'name' => $tag,
                'category_id' => $faker->numberBetween(1, 8),
            ]);
        }
    }
}
