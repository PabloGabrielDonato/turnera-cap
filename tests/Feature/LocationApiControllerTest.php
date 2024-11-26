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

    public function test_it_can_create_a_location()
    {
        $data = [
            'name' => 'Cancha de fútbol',
            'capacity' => 20,
        ];

        $response = $this->postJson('/api/locations', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('locations', $data);
    }

    public function test_it_fails_to_create_a_location_with_invalid_data()
    {
        $data = [
            'name' => '', // Nombre vacío
            'capacity' => -5, // Capacidad inválida
        ];

        $response = $this->postJson('/api/locations', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'capacity']);
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

    public function test_it_can_update_a_location()
    {
        $location = Location::factory()->create();

        $data = [
            'name' => 'Cancha de tenis',
            'capacity' => 10,
        ];

        $response = $this->putJson("/api/locations/{$location->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('locations', $data);
    }

    public function test_it_can_delete_a_location()
    {
        $location = Location::factory()->create();

        $response = $this->deleteJson("/api/locations/{$location->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_it_can_add_a_time_slot_to_a_location()
    {
        $location = Location::factory()->create();

        $data = [
            'day_of_week' => 1, // Lunes
            'start_time' => '08:00',
            'end_time' => '20:00',
            'cost_per_hour' => 1000,
        ];

        $response = $this->postJson("/api/locations/{$location->id}/time-slots", $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('time_slots', array_merge($data, ['location_id' => $location->id]));
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

    public function test_it_fails_to_create_time_slot_if_it_overlaps_with_an_existing_one()
    {
        $location = Location::factory()->create();

        // Crear un TimeSlot existente
        $location->timeSlots()->create([
            'day_of_week' => 1, // Lunes
            'start_time' => '08:00',
            'end_time' => '12:00',
            'cost_per_hour' => 1000,
        ]);

        // Intentar crear un TimeSlot que se superpone parcialmente
        $data = [
            'day_of_week' => 1, // Lunes
            'start_time' => '10:00',
            'end_time' => '14:00',
            'cost_per_hour' => 1000,
        ];

        $response = $this->postJson("/api/locations/{$location->id}/time-slots", $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'El nuevo horario se superpone con un horario existente.']);
    }

    public function test_it_can_create_time_slot_if_it_does_not_overlap()
    {
        $location = Location::factory()->create();

        // Crear un TimeSlot existente
        $location->timeSlots()->create([
            'day_of_week' => 1, // Lunes
            'start_time' => '08:00',
            'end_time' => '12:00',
            'cost_per_hour' => 1000,
        ]);

        // Intentar crear un TimeSlot que no se superpone
        $data = [
            'day_of_week' => 1, // Lunes
            'start_time' => '12:01',
            'end_time' => '14:00',
            'cost_per_hour' => 1000,
        ];

        $response = $this->postJson("/api/locations/{$location->id}/time-slots", $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'day_of_week' => 1,
                'start_time' => '12:01',
                'end_time' => '14:00',
                'cost_per_hour' => 1000,
            ]);

        $this->assertDatabaseHas('time_slots', array_merge($data, ['location_id' => $location->id]));
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
