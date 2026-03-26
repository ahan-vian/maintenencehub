<?php

namespace App\Services;

use App\Models\Sensor;
use Carbon\Carbon;

class SensorService
{
    public function preparePatchData(Sensor $sensor, array $data): array
    {
        $last = array_key_exists('last_calibrated_at', $data)
            ? $data['last_calibrated_at']
            : $sensor->last_calibrated_at;

        $interval = array_key_exists('calibration_interval_days', $data)
            ? $data['calibration_interval_days']
            : $sensor->calibration_interval_days;

        if (!empty($last)) {
            $data['next_due_date'] = Carbon::parse($last)
                ->addDays((int) $interval)
                ->toDateString();
        } elseif (array_key_exists('last_calibrated_at', $data) && $data['last_calibrated_at'] === null) {
            $data['next_due_date'] = null;
        }

        return $data;
    }
}