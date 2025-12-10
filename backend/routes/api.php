<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;

Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('drivers', DriverController::class);
Route::apiResource('trips', TripController::class);


Route::get('dashboard/summary', [DashboardController::class, 'summary']);

Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => 'Backend çalışıyor 🎉',
    ]);
});


Route::get('/redis-test', function () {
    try {
        // 60 saniyelik bir key yazar
        Redis::set('test-key', 'Merhaba Redis', 'EX', 60);

        // key reread 
        $value = Redis::get('test-key');

        return response()->json([
            'status' => 'ok',
            'value'  => $value,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});



// login - public 
Route::post('/login', [AuthController::class, 'login']);

// logout ve diger korumali endpointler 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // ileride istersem buralara da auth ekleyebilirim
    // Route::apiResource('vehicles', VehicleController::class);
    // Route::apiResource('drivers', DriverController::class);
    // Route::apiResource('trips', TripController::class);
    // Route::get('dashboard/summary', [DashboardController::class, 'summary']);
});

