<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locations')->insert([
            [
                'name' => 'Cancha de fútbol',
                'capacity' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cancha de tenis',
                'capacity' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
