<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;

class DeleteLocation
{
    public function execute(int $id):Location
    {
        $location = Location::findOrFail($id);
        $location->delete();
        return $location;
    }
}
