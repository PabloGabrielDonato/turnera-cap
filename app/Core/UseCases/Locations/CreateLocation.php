<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;

class CreateLocation
{
    public function execute(array $data): Location
    {
        return Location::create($data);
    }
}
