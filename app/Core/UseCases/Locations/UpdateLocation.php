<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;

class UpdateLocation
{
    public function execute(int $id, array $data): Location
    {
        $location = Location::findOrFail($id);
        $location->update($data);
        return $location;
    }
}
