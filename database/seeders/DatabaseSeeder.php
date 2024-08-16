<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionsSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            VideoSeeder::class,
            TagSeeder::class,
            CommentSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
