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
    private CreateLocation $createLocation;
    private ListLocations $listLocations;
    private ShowLocation $showLocation;
    private UpdateLocation $updateLocation;
    private DeleteLocation $deleteLocation;
    private AddTimeSlot $addTimeSlot;
    private GetAvailability $getAvailability;
    public function __construct(
        ListLocations $listLocations,
        CreateLocation $createLocation,
        ShowLocation $showLocation,
        UpdateLocation $updateLocation,
        DeleteLocation $deleteLocation,
        AddTimeSlot $addTimeSlot,
        GetAvailability $getAvailability,
    )
    {
        $this->listLocations = $listLocations;
        $this->createLocation = $createLocation;
        $this->showLocation = $showLocation;
        $this->updateLocation = $updateLocation;
        $this->deleteLocation = $deleteLocation;
        $this->addTimeSlot = $addTimeSlot;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $location = $this->createLocation->execute($request->all());
        return response()->json(
            $location,
            201
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $location = $this->updateLocation->execute($id, $request->all());
        return response()->json(
            $location,
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $location = $this->deleteLocation->execute($id);
        return response()->json(
            $location,
            204
        );
    }


    /**
     * Agrega Horario disponible a una locacion
     */
    public function addTimeSlot(Request $request, int $id)
    {
        $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'cost_per_hour' => 'required|integer|min:0',
        ]);

        try{
            $timeSlot = $this->addTimeSlot->execute($id, $request->all());
            return response()->json(
                $timeSlot,
                201
            );
        }catch (\Exception $exception){
           return response()->json([
                'message' => 'El nuevo horario se superpone con un horario existente.',
            ], 422);
        }
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
