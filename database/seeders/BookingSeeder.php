<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // 仮にUserテーブルに10件データがあると仮定
        $userIds = User::pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            $guide_id = $faker->randomElement($userIds);
            $guest_id = $faker->randomElement($userIds);

            // 同じIDでないように調整
            while ($guide_id === $guest_id) {
                $guest_id = $faker->randomElement($userIds);
            }

            $start_time = $faker->dateTimeBetween('now', '+1 months');
            $actual_start_time = $faker->dateTimeBetween($start_time, strtotime('+0.1 hours', $start_time->getTimestamp()));
            $end_time = $faker->dateTimeBetween($start_time, strtotime('+3 hours', $start_time->getTimestamp()));

            DB::table('bookings')->insert([
                'guide_id' => $guide_id,
                'guest_id' => $guest_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'actual_start_time' => $actual_start_time,
                'comment' => $faker->sentence,
                'total_guests' => $faker->numberBetween(1, 5),
                'status' => $faker->randomElement(['offer-pending', 'accepted', 'started', 'finished', 'reviewed', 'cancelled']),
                'guest_booking_confirmation' => false,
                'guide_booking_confirmation' => false,
                'start_confirmation' => false,
                'guest_reviewed' => false,
                'guide_reviewed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
