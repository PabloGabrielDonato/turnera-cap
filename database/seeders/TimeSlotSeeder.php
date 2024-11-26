<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('time_slots')->insert([
            [
                'location_id' => 1,
                'day_of_week' => 1, // Lunes
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'cost_per_hour' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'location_id' => 2,
                'day_of_week' => 2, // Martes
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'cost_per_hour' => 1200,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
