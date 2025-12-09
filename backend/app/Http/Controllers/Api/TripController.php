<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Trip::with(['vehicle', 'driver']);

        if ($request->has('status') && $request->status) {
            $statuses = (array) $request->status;
            $query->whereIn('status', $statuses);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('start_time', $request->date);
        }

        return response()->json(
            $query->orderBy('start_time', 'desc')->paginate(20)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'  => 'required|exists:vehicles,id',
            'driver_id'   => 'required|exists:drivers,id',
            'start_time'  => 'required|date',
            'end_time'    => 'nullable|date|after_or_equal:start_time',
            'origin'      => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'status'      => 'required|in:planned,ongoing,completed,canceled',
            'notes'       => 'nullable|string',
        ]);

        $trip = Trip::create($data)->load(['vehicle', 'driver']);

        return response()->json($trip, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        $trip->load(['vehicle', 'driver']);

        return response()->json($trip);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'vehicle_id'  => 'sometimes|required|exists:vehicles,id',
            'driver_id'   => 'sometimes|required|exists:drivers,id',
            'start_time'  => 'sometimes|required|date',
            'end_time'    => 'nullable|date|after_or_equal:start_time',
            'origin'      => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'status'      => 'sometimes|required|in:planned,ongoing,completed,canceled',
            'notes'       => 'nullable|string',
        ]);

        $trip->update($data);

        $trip->load(['vehicle', 'driver']);

        return response()->json($trip);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        $trip->delete();

        return response()->json(null, 204);
    }
}
