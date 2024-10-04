<?php

namespace App\Api\WeatherApi;

interface WeatherApiInterface
{
    public function getWeather(
        string $city, string $state, string $country,
        string $customResponseTransformer = null
    ): WeatherResponseInterface;
}