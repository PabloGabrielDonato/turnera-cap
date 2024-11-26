<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_bookings()
    {
        Booking::factory()->count(3)->create();

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_it_can_create_booking_within_time_slot()
    {
        $location = Location::factory()->create(['capacity' => 20]);
        $user = User::factory()->create();

        // Crear un TimeSlot válido
        $location->timeSlots()->create([
            'day_of_week' => 3, // Miércoles
            'start_time' => '10:00',
            'end_time' => '12:00',
            'cost_per_hour' => 1000,
        ]);

        // Crear una reserva dentro del TimeSlot
        $data = [
            'location_id' => $location->id,
            'user_id' => $user->id,
            'start_time' => '2024-11-20 10:30:00',
            'end_time' => '2024-11-20 11:30:00',
            'people_count' => 10,
        ];

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'location_id' => $location->id,
                'user_id' => $user->id,
                'start_time' => '2024-11-20 10:30:00',
                'end_time' => '2024-11-20 11:30:00',
                'people_count' => 10,
            ]);

        $this->assertDatabaseHas('bookings', $data);
    }


    public function test_it_fails_to_create_booking_outside_time_slots()
    {
        $location = Location::factory()->create(['capacity' => 20]);
        $user = User::factory()->create();

        // Crear un TimeSlot que solo permita reservas entre las 10:00 y las 12:00
        $location->timeSlots()->create([
            'day_of_week' => 3, // Miércoles
            'start_time' => '10:00',
            'end_time' => '12:00',
            'cost_per_hour' => 1000,
        ]);

        // Intentar crear una reserva fuera del TimeSlot
        $data = [
            'location_id' => $location->id,
            'user_id' => $user->id,
            'start_time' => '2024-11-20 08:00:00',
            'end_time' => '2024-11-20 09:30:00',
            'people_count' => 10,
        ];

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'El horario solicitado no está disponible.']);
    }


    public function test_it_fails_to_create_booking_if_capacity_exceeds()
    {
        $location = Location::factory()->create(['capacity' => 20]);
        $user = User::factory()->create();

        // Crear un TimeSlot válido
        $location->timeSlots()->create([
            'day_of_week' => 3, // Miércoles
            'start_time' => '10:00',
            'end_time' => '12:00',
            'cost_per_hour' => 1000,
        ]);

        // Crear una reserva que ocupe la mayor parte de la capacidad
        Booking::factory()->create([
            'location_id' => $location->id,
            'user_id' => $user->id,
            'start_time' => '2024-11-20 10:00:00',
            'end_time' => '2024-11-20 11:00:00',
            'people_count' => 15,
        ]);

        // Verificar que la capacidad inicial está correctamente configurada
        $this->assertDatabaseHas('locations', ['id' => $location->id, 'capacity' => 20]);

        // Intentar crear otra reserva que exceda la capacidad
        $data = [
            'location_id' => $location->id,
            'user_id' => $user->id,
            'start_time' => '2024-11-20 10:30:00',
            'end_time' => '2024-11-20 11:30:00',
            'people_count' => 10,
        ];

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Capacidad excedida para este horario.']);
    }

    public function test_it_can_show_a_booking()
    {
        $booking = Booking::factory()->create();

        $response = $this->getJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $booking->id,
                'location_id' => $booking->location_id,
                'user_id' => $booking->user_id,
                'people_count' => $booking->people_count,
            ]);

        $response->assertJsonPath('start_time', $booking->start_time->format('Y-m-d H:i:s'));
        $response->assertJsonPath('end_time', $booking->end_time->format('Y-m-d H:i:s'));
    }

    public function test_it_can_update_a_booking()
    {
        $booking = Booking::factory()->create();

        $data = [
            'start_time' => '2024-11-20 12:00:00',
            'end_time' => '2024-11-20 13:30:00',
            'people_count' => 5,
        ];

        $response = $this->putJson("/api/bookings/{$booking->id}", $data);

        $response->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('bookings', array_merge($data, ['id' => $booking->id]));
    }

    public function test_it_can_delete_a_booking()
    {
        $booking = Booking::factory()->create();

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_it_fails_to_create_booking_if_location_has_no_time_slots()
    {
        $location = Location::factory()->create(['capacity' => 20]);
        $user = User::factory()->create();

        // Intentar crear una reserva para una locación sin TimeSlots
        $data = [
            'location_id' => $location->id,
            'user_id' => $user->id,
            'start_time' => '2024-11-20 10:00:00',
            'end_time' => '2024-11-20 11:30:00',
            'people_count' => 10,
        ];

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'No se pueden crear reservas porque la locación no tiene horarios disponibles.']);
    }
}
