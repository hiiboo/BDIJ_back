<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Booking;


class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $userIds = User::pluck('id')->toArray();
        $bookingIds = Booking::pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            $reviewer_id = $faker->randomElement($userIds);
            $reviewee_id = $faker->randomElement($userIds);

            // Ensure that the reviewer_id and reviewee_id are different
            while ($reviewer_id === $reviewee_id) {
                $reviewee_id = $faker->randomElement($userIds);
            }

            $booking_id = $faker->randomElement($bookingIds);

            DB::table('reviews')->insert([
                'reviewer_id' => $reviewer_id,
                'reviewee_id' => $reviewee_id,
                'booking_id' => $booking_id,
                'rating' => $faker->numberBetween(1, 5),
                'content' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
