<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of category names to seed
        $categories = [
            'IT',
            'Cultures',
            'Design',
            'Arts',
            'Business',
            'Self-Dev',
            'Health',
            'Science',
        ];

        // Iterate over each category and create a new record
        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
            ]);
        }
    }
}
