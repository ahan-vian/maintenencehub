<?php

namespace App\Listeners;

use App\Events\SensorPatched;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSensorPatchedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SensorPatched $event): void
    {
        ActivityLog::create([
            'user_id' => $event->userId,
            'action' => 'sensor.patched',
            'entity_type' => 'sensor',
            'entity_id' => $event->sensor->id,
            'meta' => $event->changes,
        ]);
    }
}