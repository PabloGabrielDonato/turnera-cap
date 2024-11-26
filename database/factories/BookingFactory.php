<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 week');
        $endTime = (clone $startTime)->modify('+1 hour');
        return [
            'location_id' => Location::factory(),
            'user_id' => User::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'people_count' => $this->faker->numberBetween(1, 20),
        ];
    }
}
