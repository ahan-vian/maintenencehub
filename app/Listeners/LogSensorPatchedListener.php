<?php

namespace App\Listeners;

use App\Events\SensorPatched;
use App\Models\ActivityLog;

class LogSensorPatchedListener
{
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