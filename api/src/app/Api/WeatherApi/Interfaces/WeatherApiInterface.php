<?php

namespace App\Api\WeatherApi\Interfaces;

interface WeatherApiInterface
{
    public function getWeather(
        string $city,
        string $country,
        string $customResponseTransformer = null
    ): WeatherResponseInterface;
}
