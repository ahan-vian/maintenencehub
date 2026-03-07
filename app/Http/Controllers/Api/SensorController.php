<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Models\Sensor;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PatchSensorRequest;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);

        $filters = $request->only(['location_id', 'status']);

        $sensors = Sensor::query()
            ->with('location')
            ->filter($filters)
            ->search($request->query('q'))
            ->sort($request->query('sort_by'), $request->query('sort_dir'))
            ->paginate($perPage);

        return ApiResponse::success($sensors, 'Sensors fetched');
    }

    public function store(StoreSensorRequest $request)
    {
        $data = $request->validated();

        // hitung next_due_date kalau last_calibrated_at ada
        if (!empty($data['last_calibrated_at'])) {
            $interval = $data['calibration_interval_days'] ?? 90;
            $data['next_due_date'] = now()->parse($data['last_calibrated_at'])->addDays($interval)->toDateString();
        }

        $sensor = Sensor::create($data)->load('location');

        return ApiResponse::success($sensor, 'Sensor created', 201);
    }

    public function show(Sensor $sensor)
    {
        $sensor->load('location');
        return ApiResponse::success($sensor, 'Sensor fetched');
    }

    public function update(UpdateSensorRequest $request, Sensor $sensor)
    {
        $this->authorize('update', $sensor);
        $data = $request->validated();

        if (!empty($data['last_calibrated_at'])) {
            $interval = $data['calibration_interval_days'] ?? $sensor->calibration_interval_days;
            $data['next_due_date'] = now()->parse($data['last_calibrated_at'])->addDays($interval)->toDateString();
        }

        $sensor->update($data);

        return ApiResponse::success($sensor->fresh()->load('location'), 'Sensor updated');
    }

    public function destroy(Sensor $sensor)
    {
        $this->authorize('delete', $sensor);
        $sensor->delete();
        return ApiResponse::success(null, 'Sensor deleted');
    }

    public function patch(PatchSensorRequest $request, Sensor $sensor)
    {
        $this->authorize('update', $sensor);

        $data = $request->validated();

        // kalau interval berubah tapi last_calibrated_at tidak dikirim, pakai existing last_calibrated_at
        $last = array_key_exists('last_calibrated_at', $data) ? $data['last_calibrated_at'] : $sensor->last_calibrated_at;
        $interval = array_key_exists('calibration_interval_days', $data) ? $data['calibration_interval_days'] : $sensor->calibration_interval_days;

        // next_due_date dihitung kalau punya last date (baik existing atau new)
        if (!empty($last)) {
            $data['next_due_date'] = now()->parse($last)->addDays((int) $interval)->toDateString();
        } else {
            // kalau last_calibrated_at di-patch jadi null, due date juga null
            if (array_key_exists('last_calibrated_at', $data) && $data['last_calibrated_at'] === null) {
                $data['next_due_date'] = null;
            }
        }

        $sensor->update($data);

        return ApiResponse::success($sensor->fresh()->load('location'), 'Sensor patched');
    }
}