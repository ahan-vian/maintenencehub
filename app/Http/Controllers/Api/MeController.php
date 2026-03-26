<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function locations(Request $request)
    {
        $user = $request->user();

        // admin bisa lihat semua locations (opsional, tapi enak)
        if ($user->hasRole('admin')) {
            $locations = \App\Models\Location::query()->latest()->get();
            return ApiResponse::success($locations, 'All locations (admin)');
        }

        // selain admin: hanya yang assigned
        $locations = $user->locations()->latest()->get();

        return ApiResponse::success($locations, 'My locations');
    }

    public function sensors(Request $request)
    {
        $user = $request->user();

        $perPage = (int) $request->query('per_page', 10);

        // admin: semua sensor
        $query = Sensor::query()->with('location');

        if (!$user->hasRole('admin')) {
            $locationIds = $user->locations()->pluck('locations.id');
            $query->whereIn('location_id', $locationIds);
        }

        // re-use scopes dari Day 4
        $filters = $request->only(['status', 'location_id']);
        $sensors = $query
            ->filter($filters)
            ->search($request->query('q'))
            ->sort($request->query('sort_by'), $request->query('sort_dir'))
            ->paginate($perPage);

        return ApiResponse::success($sensors, 'My sensors');
    }
}