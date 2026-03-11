<?php

namespace App\Listeners;

use App\Events\SensorPatched;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSensorPatchedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $backoff = 10;

    public function handle(SensorPatched $event): void
    {
        throw new \Exception('Simulated queue failure for testing');
        ActivityLog::create([
            'user_id' => $event->userId,
            'action' => 'sensor.patched',
            'entity_type' => 'sensor',
            'entity_id' => $event->sensor->id,
            'meta' => $event->changes,
        ]);
    }

    public function failed(SensorPatched $event, \Throwable $exception): void
    {
        \Log::error('LogSensorPatchedListener failed', [
            'sensor_id' => $event->sensor->id,
            'user_id' => $event->userId,
            'changes' => $event->changes,
            'error' => $exception->getMessage(),
        ]);
    }
}