<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Models\Sensor;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PatchSensorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Services\SensorService;
use App\Jobs\RecalculateSensorDueDate;
use App\Jobs\LogSensorPatched;

class SensorController extends Controller
{
    protected SensorService $sensorService;

    public function __construct(SensorService $sensorService)
    {
        $this->sensorService = $sensorService;
    }


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

        $data = $this->sensorService->preparePatchData(
            $sensor,
            $request->validated()
        );

        $sensor->update($data);

        return ApiResponse::success(
            $sensor->fresh()->load('location'),
            'Sensor updated'
        );
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

        $data = $this->sensorService->preparePatchData(
            $sensor,
            $request->validated()
        );

        $sensor->update($data);

        LogSensorPatched::dispatch(
            auth()->id(),
            $sensor->id,
            $data
        );

        return \App\Support\ApiResponse::success(
            $sensor->fresh()->load('location'),
            'Sensor patched'
        );
    }
}