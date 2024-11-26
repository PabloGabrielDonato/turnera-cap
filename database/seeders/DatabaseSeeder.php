<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $roles = ['admin', 'member'];

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'member']);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
        ]);
        $admin->assignRole('admin');

        $this->call([
        //   UserSeeder::class,
        //   LocationSeeder::class,
        //   TimeSlotSeeder::class,
        //   BookingSeeder::class,
        ]);

      //  Locations::factory()->create(3);
      //  Booking::factory()->create();

    }
}
