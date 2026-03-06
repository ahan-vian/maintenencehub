<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// protect resources
Route::middleware('auth:sanctum')->group(function () {

    // Locations
    Route::get('/locations', [LocationController::class, 'index'])->middleware('permission:locations.read');
    Route::get('/locations/{location}', [LocationController::class, 'show'])->middleware('permission:locations.read');
    Route::post('/locations', [LocationController::class, 'store'])->middleware('permission:locations.manage');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->middleware('permission:locations.manage');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->middleware('permission:locations.manage');

    // Sensors
    Route::get('/sensors', [SensorController::class, 'index'])->middleware('permission:sensors.read');
    Route::get('/sensors/{sensor}', [SensorController::class, 'show'])->middleware('permission:sensors.read');
    Route::post('/sensors', [SensorController::class, 'store'])->middleware('permission:sensors.manage');
    Route::put('/sensors/{sensor}', [SensorController::class, 'update'])->middleware('permission:sensors.manage');
    Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])->middleware('permission:sensors.delete');
});