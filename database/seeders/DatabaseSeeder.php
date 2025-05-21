<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $faker = Faker::create();

        for ($i = 1; $i <= 50; $i++) {
            Notice::create([
                'title' => $faker->sentence(6), // Generates a realistic title
                'categories' => $faker->randomElement(['Administration', 'Academic', 'Department']),
                'department' => $faker->randomElement(['CSE', 'EEE', 'BBA', 'ME', 'CE', null]),
                'published_at' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
