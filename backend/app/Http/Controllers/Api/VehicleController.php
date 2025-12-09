<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;


class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->has('status') && $request->status) {
            // status[]=active&status[]=in_service gibi çoklu filtre destekleyelim
            $statuses = (array) $request->status;
            $query->whereIn('status', $statuses);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plate'       => 'required|string|max:50|unique:vehicles,plate',
            'brand'       => 'required|string|max:100',
            'model'       => 'required|string|max:100',
            'year'        => 'nullable|integer',
            'current_km'  => 'nullable|integer|min:0',
            'status'      => 'nullable|in:active,in_service,inactive',
        ]);

        $vehicle = Vehicle::create($data);

        return response()->json($vehicle, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'plate'       => 'sometimes|required|string|max:50|unique:vehicles,plate,' . $vehicle->id,
            'brand'       => 'sometimes|required|string|max:100',
            'model'       => 'sometimes|required|string|max:100',
            'year'        => 'nullable|integer',
            'current_km'  => 'nullable|integer|min:0',
            'status'      => 'nullable|in:active,in_service,inactive',
        ]);

        $vehicle->update($data);

        return response()->json($vehicle);
    }
     

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json(null, 204);
    }
}
