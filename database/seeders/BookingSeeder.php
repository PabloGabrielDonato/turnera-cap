<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bookings')->insert([
            [
                'location_id' => 1,
                'user_id' => 1,
                'start_time' => '2024-11-20 10:00:00',
                'end_time' => '2024-11-20 11:30:00',
                'people_count' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'location_id' => 2,
                'user_id' => 2,
                'start_time' => '2024-11-21 15:00:00',
                'end_time' => '2024-11-21 16:30:00',
                'people_count' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
