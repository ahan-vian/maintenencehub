<?php

namespace App\Listeners;

use App\Events\SensorPatched;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\SensorPatchedNotification;

class LogSensorPatchedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $backoff = 10;

    public function handle(SensorPatched $event): void
    {
        ActivityLog::create([
            'user_id' => $event->userId,
            'action' => 'sensor.patched',
            'entity_type' => 'sensor',
            'entity_id' => $event->sensor->id,
            'meta' => $event->changes,
        ]);

        // contoh sederhana: kirim notifikasi ke semua admin
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new SensorPatchedNotification(
                    $event->sensor,
                    $event->changes,
                    $event->userId
                )
            );
        }
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