<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
     use HasFactory;

    protected $fillable = [
        'plate',
        'brand',
        'model',
        'year',
        'current_km',
        'status',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
