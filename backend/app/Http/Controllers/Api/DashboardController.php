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

    //dashboard'da gerekenleri hesaplayip JSON olarak return ediyorum 
    public function summary()
    {
        $today = Carbon::today(); //bugunun tarihini aliyorum

        $totalVehicles      = Vehicle::count();
        $vehiclesInService  = Vehicle::where('status', 'in_service')->count();
        $activeTrips        = Trip::whereIn('status', ['planned', 'ongoing'])->count();
        $tripsToday         = Trip::whereDate('start_time', $today)->count(); //whereDate saatten bagimsiz sadece YYYY-MM-DD kismina bakar 

        return response()->json([
            'totalVehicles'     => $totalVehicles,
            'vehiclesInService' => $vehiclesInService,
            'activeTrips'       => $activeTrips,
            'tripsToday'        => $tripsToday,
        ]);
    }
}



