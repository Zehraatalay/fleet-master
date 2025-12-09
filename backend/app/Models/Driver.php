<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'license_type',
        'status',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
