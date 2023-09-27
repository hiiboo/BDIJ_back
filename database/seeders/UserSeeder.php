<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $faker = \Faker\Factory::create();

        foreach (range(1, 50) as $index) {
            $latitude = $faker->latitude(35.50, 35.84);
            $longitude = $faker->longitude(139.58, 139.84);

            DB::table('users')->insert([
                'login_type' => $faker->randomElement([1, 2, 3, 4, 5]),
                'social_id' => $faker->uuid,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'profile_image' => $faker->imageUrl(),
                'level' => $faker->randomElement([
                    'beginner',
                    'elementary',
                    'intermediate',
                    'upper_intermediate',
                    'advanced',
                    'proficiency',
                ]),
                'introduction' => $faker->text,
                'hourly_rate' => $faker->numberBetween(10, 100),
                'birthday' => $faker->date,
                'occupation' => $faker->word,
                'user_type' => $faker->randomElement(['guide', 'guest']),
                'status' => $faker->randomElement(['active', 'inactive']),
                'role' => $faker->randomElement(['admin', 'user']),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // パスワード
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
