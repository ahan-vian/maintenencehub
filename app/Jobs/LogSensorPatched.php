<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogSensorPatched implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public int $sensorId;
    public array $changes;

    public function __construct(int $userId, int $sensorId, array $changes)
    {
        $this->userId = $userId;
        $this->sensorId = $sensorId;
        $this->changes = $changes;
    }

    public function handle(): void
    {
        ActivityLog::create([
            'user_id' => $this->userId,
            'action' => 'sensor.patched',
            'entity_type' => 'sensor',
            'entity_id' => $this->sensorId,
            'meta' => $this->changes,
        ]);
    }
}