<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForecastModel extends Model
{
    use HasFactory;

    protected $table = 'forecast';

    protected static function booted()
    {
        static::creating(function (ForecastModel $forecast) {
            $forecast->uuid = (string) Str::uuid();
            $forecast->weather_data = json_encode($forecast->weather_data);
        });

        static::retrieved(function (ForecastModel $forecast) {
            $forecast->weather_data = json_decode($forecast->weather_data, true);
        });
    }
}
