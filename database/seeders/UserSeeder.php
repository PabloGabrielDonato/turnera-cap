<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Juan',
                'last_name' => 'Perez',
                'email' => 'juan.perez@example.com',
                'password' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ana',
                'last_name' => 'Lopez',
                'email' => 'ana.lopez@example.com',
                'password' => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
