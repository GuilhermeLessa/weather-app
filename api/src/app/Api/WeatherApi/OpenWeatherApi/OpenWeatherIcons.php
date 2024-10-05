<?php

namespace App\Api\WeatherApi\OpenWeatherApi;

class OpenWeatherIcons
{
    const BASE_URL = "https://openweathermap.org/img/wn/";
    const FILE_EXT = ".png";

    static function url(string $iconFileName): string
    {
        return self::BASE_URL . $iconFileName . self::FILE_EXT;
    }
}
