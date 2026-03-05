<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;
    protected $fillable = [
        'location_id',
        'name',
        'serial_number',
        'type',
        'status',
        'last_calibrated_at',
        'calibration_interval_days',
        'next_due_date'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
