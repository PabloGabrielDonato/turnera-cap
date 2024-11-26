<?php

namespace App\Core\UseCases\Locations;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class ListLocations
{
    public function execute(): Collection
    {
        return Location::all();
    }
}
