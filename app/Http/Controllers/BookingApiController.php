<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Location;
use Illuminate\Http\Request;

class BookingApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()
            ->json(Booking::with('location','user')
                ->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date|before:end_time',
            'end_time' => 'required|date|after:start_time',
            'people_count' => 'required|integer|min:1',
        ]);

        $location = Location::findOrFail($request->location_id);

        // Verificar si la locación tiene TimeSlots
        if ($location->timeSlots->isEmpty()) {
            return response()->json([
                'message' => 'No se pueden crear reservas porque la locación no tiene horarios disponibles.',
            ], 422);
        }

        // Verificar si el horario solicitado está dentro de un TimeSlot
        $timeSlot = $location->timeSlots()
            ->where('day_of_week', \Carbon\Carbon::parse($request->start_time)->dayOfWeek)
            ->where('start_time', '<=', \Carbon\Carbon::parse($request->start_time)->format('H:i'))
            ->where('end_time', '>=', \Carbon\Carbon::parse($request->end_time)->format('H:i'))
            ->first();

        if (!$timeSlot) {
            return response()->json([
                'message' => 'El horario solicitado no está disponible.',
            ], 422);
        }

        // Verificar capacidad
        $overlapBookings = $location->bookings()
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->sum('people_count');


        if ($overlapBookings + $request->people_count > $location->capacity) {
            return response()->json([
                'message' => 'Capacidad excedida para este horario.',
            ], 422);
        }

        // Crear reserva
        $booking = Booking::create($request->all());

        return response()->json($booking, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::findOrFail($id);
        return response()->json($booking, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'start_time' => 'sometimes|date|before:end_time',
            'end_time' => 'sometimes|date|after:start_time',
            'people_count' => 'sometimes|integer|min:1',
        ]);

        $booking->update($request->all());
        return response()->json($booking, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json($booking, 204);
    }
}
