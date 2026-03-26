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

    public function scopeSearch($query, ?string $q)
    {
        if (!$q)
            return $query;

        return $query->where(function ($qq) use ($q) {
            $qq->where('name', 'like', "%{$q}%")
                ->orWhere('serial_number', 'like', "%{$q}%");
        });
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['location_id'] ?? null, fn($q, $v) => $q->where('location_id', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v));
    }

    public function scopeSort($query, ?string $sortBy, ?string $sortDir)
    {
        $allowed = ['name', 'created_at', 'next_due_date'];
        $sortBy = in_array($sortBy, $allowed, true) ? $sortBy : 'created_at';

        $sortDir = strtolower($sortDir ?? 'desc');
        $sortDir = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : 'desc';

        return $query->orderBy($sortBy, $sortDir);
    }
}
