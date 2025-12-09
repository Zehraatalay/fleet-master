<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver; 
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function summary()
    {
        $today = Carbon::today();

        $totalVehicles      = Vehicle::count();
        $vehiclesInService  = Vehicle::where('status', 'in_service')->count();
        $activeTrips        = Trip::whereIn('status', ['planned', 'ongoing'])->count();
        $tripsToday         = Trip::whereDate('start_time', $today)->count();

        return response()->json([
            'totalVehicles'     => $totalVehicles,
            'vehiclesInService' => $vehiclesInService,
            'activeTrips'       => $activeTrips,
            'tripsToday'        => $tripsToday,
        ]);
    }
}
