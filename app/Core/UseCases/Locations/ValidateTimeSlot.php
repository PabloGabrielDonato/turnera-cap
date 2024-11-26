<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;
use App\Models\TimeSlot;

class ValidateTimeSlot
{
    public function execute(int $locationId, array $data): bool
    {
        $location = Location::findOrFail($locationId);

        // Verificar superposición de TimeSlots
        $overlappingSlot = $location->timeSlots()
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($query) use ($data) {
                        $query->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        return !$overlappingSlot;
    }
}
