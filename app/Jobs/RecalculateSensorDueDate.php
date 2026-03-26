<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Sensor;
use App\Services\SensorService;
class RecalculateSensorDueDate implements ShouldQueue
{
    protected $sensor;
    protected $data;

    public function __construct(Sensor $sensor, array $data)
    {
        $this->sensor = $sensor;
        $this->data = $data;
    }

    public function handle(SensorService $sensorService)
    {
        // Panggil service untuk perhitungan ulang
        $sensorService->recalculateNextDueDate($this->sensor, $this->data);

        // Simpan perubahan ke database
        $this->sensor->save();
    }
}
