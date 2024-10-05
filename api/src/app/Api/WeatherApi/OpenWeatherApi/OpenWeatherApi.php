<?php

namespace App\Api\WeatherApi\OpenWeatherApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

use App\Api\WeatherApi\Interfaces\WeatherApiInterface;
use App\Api\WeatherApi\Interfaces\WeatherResponseInterface;
use App\Api\WeatherApi\Exceptions\CityIsNotDefined;
use App\Api\WeatherApi\Exceptions\CityNotFound;
use App\Api\WeatherApi\Exceptions\CountryIsNotDefined;

class OpenWeatherApi implements WeatherApiInterface
{
    private string $baseUrl;

    function __construct(private string $apiKey)
    {
        $this->baseUrl = env("OPEN_WEATHER_MAP_API_URL");
    }

    function getWeather(
        string $city,
        string $country,
        string $customResponseTransformer = null
    ): OpenWeatherResponse {
        if (empty($city)) {
            throw new CityIsNotDefined("City is not defined");
        }

        if (empty($country)) {
            throw new CountryIsNotDefined("Country is not defined");
        }

        /**
         * @var Response
         */
        $response = $this->get("/weather?q={$city},{$country}");

        if ($response->status() === 404) {
            throw new CityNotFound("City not found");
        }

        if ($customResponseTransformer) {
            /**
             * @var WeatherResponseInterface
             */
            $weatherResponse = new $customResponseTransformer($response->json());
            return $weatherResponse;
        }

        /**
         * @var OpenWeatherResponse
         */
        $weatherResponse = new OpenWeatherResponse($response->json());
        return $weatherResponse;
    }

    private function get(string $url): Response
    {
        /**
         * @var Response
         */
        $response = Http::get("{$this->baseUrl}{$url}&appid={$this->apiKey}");
        return $response;
    }
}
