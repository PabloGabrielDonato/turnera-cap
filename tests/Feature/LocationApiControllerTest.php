<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\TimeSlot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_locations()
    {
        Location::factory()->count(3)->create();

        $response = $this->getJson('/api/locations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'capacity', 'created_at', 'updated_at'],
            ])
            ->assertJsonCount(3);
    }

    public function test_it_can_show_a_location()
    {
        $location = Location::factory()->create();

        $response = $this->getJson("/api/locations/{$location->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $location->id,
                'name' => $location->name,
                'capacity' => $location->capacity,
            ]);
    }

    public function test_it_checks_availability_based_on_time_slots()
    {
        $location = Location::factory()->create(['capacity' => 20]);

        // Crear dos TimeSlots
        $location->timeSlots()->createMany([
            [
                'day_of_week' => 1, // Lunes
                'start_time' => '08:00',
                'end_time' => '12:00',
                'cost_per_hour' => 1000,
            ],
            [
                'day_of_week' => 1, // Lunes
                'start_time' => '14:00',
                'end_time' => '18:00',
                'cost_per_hour' => 1000,
            ],
        ]);

        // Consultar disponibilidad para un lunes
        $response = $this->getJson("/api/locations/{$location->id}/availability?date=2024-11-18");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'start_time' => '08:00',
                'end_time' => '12:00',
                'available_capacity' => 20,
            ])
            ->assertJsonFragment([
                'start_time' => '14:00',
                'end_time' => '18:00',
                'available_capacity' => 20,
            ])
            ->assertJsonMissing([
                'start_time' => '12:00',
                'end_time' => '14:00',
            ]);
    }

    public function test_it_fails_to_check_availability_without_date_parameter()
    {
        $location = Location::factory()->create(['capacity' => 20]);

        $response = $this->getJson("/api/locations/{$location->id}/availability");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'El parámetro "date" es obligatorio.',
            ]);
    }
}
