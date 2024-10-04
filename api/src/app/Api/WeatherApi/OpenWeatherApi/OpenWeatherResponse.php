<?php

namespace App\Api\WeatherApi\OpenWeatherApi;

use Exception;

use App\Api\WeatherApi\WeatherResponseInterface;
use App\Api\WeatherApi\OpenWeatherApi\Exceptions\MalformedForecastData;

class OpenWeatherResponse implements WeatherResponseInterface
{
    function __construct(private mixed $data) {}

    public function getData(): mixed
    {
        return $this->data;
    }

    public function toArray(): array
    {
        try {
            return [
                'description' => $this->data['weather'][0]['description'],
                'temperature' => $this->data['main']['temp'],
                'minimumTemperature' => $this->data['main']['temp_min'],
                'maximumTemperature' => $this->data['main']['temp_max'],
                'humidity' => $this->data['main']['humidity'],
                'wind' => $this->data['wind']['speed'],
                'city' => $this->data['name']
            ];
        } catch (Exception $e) {
            throw new MalformedForecastData();
        }
    }
}