<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver; 

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Driver::query();

        if ($request->has('status') && $request->status) {
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
            'name'         => 'required|string|max:150',
            'phone'        => 'nullable|string|max:50',
            'license_type' => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,inactive',
        ]);

        $driver = Driver::create($data);

        return response()->json($driver, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        return response()->json($driver);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name'         => 'sometimes|required|string|max:150',
            'phone'        => 'nullable|string|max:50',
            'license_type' => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,inactive',
        ]);

        $driver->update($data);

        return response()->json($driver);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        $driver->delete();

        return response()->json(null, 204);
    }
}
