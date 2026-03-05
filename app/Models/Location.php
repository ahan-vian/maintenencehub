<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        "name","building","floor","note"
    ];
    public function sensors(){
        return $this->hasMany(Sensor::class);
    }
}
