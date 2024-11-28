<?php

namespace App\Http\Controllers;


use App\Core\UseCases\Locations\AddTimeSlot;
use App\Core\UseCases\Locations\CreateLocation;
use App\Core\UseCases\Locations\DeleteLocation;
use App\Core\UseCases\Locations\GetAvailability;
use App\Core\UseCases\Locations\ListLocations;
use App\Core\UseCases\Locations\ShowLocation;
use App\Core\UseCases\Locations\UpdateLocation;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    private ListLocations $listLocations;
    private ShowLocation $showLocation;
    private GetAvailability $getAvailability;
    public function __construct(
        ListLocations $listLocations,
        ShowLocation $showLocation,
        GetAvailability $getAvailability,
    )
    {
        $this->listLocations = $listLocations;
        $this->showLocation = $showLocation;
        $this->getAvailability = $getAvailability;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            $this->listLocations->execute(),
            200
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $location = $this->showLocation->execute($id);
        return response()->json(
            $location,
            200
        );
    }

    /**
     * Verificar Disponibilidad de una locacion
     */
    public function getAvailability(Request $request, int $id)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json([
                'message' => 'El parámetro "date" es obligatorio.',
            ], 422);
        }

        $availability = $this->getAvailability->execute($id, $date);
        return response()->json($availability, 200);
    }
}
