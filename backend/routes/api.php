<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;

// Public authentication endpoint
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    // Fleet management
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('trips', TripController::class);

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
});