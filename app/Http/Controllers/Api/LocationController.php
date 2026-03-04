<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);

        $locations = Location::query()
            ->latest()
            ->paginate($perPage);

        return ApiResponse::success($locations, 'Locations fetched');
    }

    public function store(StoreLocationRequest $request)
    {
        $location = Location::create($request->validated());

        return ApiResponse::success($location, 'Location created', 201);
    }

    public function show(Location $location)
    {
        return ApiResponse::success($location, 'Location fetched');
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->validated());

        return ApiResponse::success($location, 'Location updated');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return ApiResponse::success(null, 'Location deleted', 200);
    }
}