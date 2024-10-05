<?php

namespace App\Api\WeatherApi\OpenWeatherApi;

use Exception;

use App\Api\WeatherApi\Interfaces\WeatherResponseInterface;
use App\Api\WeatherApi\Exceptions\MalformedResponseData;

class OpenWeatherResponse implements WeatherResponseInterface
{

    private readonly string $city;
    private readonly string $country;
    private readonly string $description;
    private readonly string $icon;
    private readonly string $temperature;
    private readonly string $minimumTemperature;
    private readonly string $maximumTemperature;
    private readonly string $humidity;
    private readonly string $wind;

    function __construct(
        private readonly array $data
    ) {
        try {
            $this->city = $this->data['name'];
            $this->country = $this->data['sys']['country'];
            $this->description = $this->data['weather'][0]['description'];
            $this->icon = OpenWeatherIcons::url($this->data['weather'][0]['icon']);
            $this->temperature = $this->data['main']['temp'];
            $this->minimumTemperature = $this->data['main']['temp_min'];
            $this->maximumTemperature = $this->data['main']['temp_max'];
            $this->humidity = $this->data['main']['humidity'];
            $this->wind = $this->data['wind']['speed'];
        } catch (Exception $e) {
            throw new MalformedResponseData("Malformed weather data");
        }
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
            'description' => $this->description,
            'icon' => $this->icon,
            'temperature' => $this->temperature,
            'minimumTemperature' => $this->minimumTemperature,
            'maximumTemperature' => $this->maximumTemperature,
            'humidity' => $this->humidity,
            'wind' => $this->wind
        ];
    }
}
