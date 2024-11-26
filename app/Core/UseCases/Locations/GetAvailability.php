<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;

class GetAvailability
{
    public function execute(int $locationId, string $date):array
    {
        $location = Location::findOrFail($locationId);

        $availability = [];
        $timeSlots = $location->timeSlots;

        foreach ($timeSlots as $slot) {
            $bookedPeople = $location->bookings()
                ->whereDate('start_time', $date)
                ->where(function ($query) use ($slot) {
                    $query
                        ->whereBetween('start_time', [$slot->start_time, $slot->end_time])
                        ->orWhereBetween('end_time', [$slot->start_time, $slot->end_time]);
                })
                ->sum('people_count');

            $availability[] = [
                'start_time' => \Carbon\Carbon::createFromFormat('H:i:s', $slot->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::createFromFormat('H:i:s', $slot->end_time)->format('H:i'),
                'available_capacity' => max(0, $location->capacity - $bookedPeople),
            ];
        }

        return $availability;
    }
}
