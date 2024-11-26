<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;

class ShowLocation
{
    public function execute(int $id): Location
    {
        return Location::findOrFail($id);
    }
}
