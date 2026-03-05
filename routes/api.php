<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SensorController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/health', function (){
    return response()->json([
        'status'=> 'ok',
        'app'=>config('app.name'),
        'time'=>now()->toDateTimeString()
    ]);
});
Route::apiResource('locations', LocationController::class);

Route::apiResource('sensors', SensorController::class);
