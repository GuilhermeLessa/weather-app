<?php

namespace App\Api\WeatherApi\OpenWeatherApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

use App\Api\WeatherApi\WeatherApiInterface;
use App\Api\WeatherApi\WeatherResponseInterface;
use App\Api\WeatherApi\OpenWeatherApi\Exceptions\CityIsNotDefined;
use App\Api\WeatherApi\OpenWeatherApi\Exceptions\CityNotFound;
use App\Api\WeatherApi\OpenWeatherApi\Exceptions\CountryIsNotDefined;
use App\Api\WeatherApi\OpenWeatherApi\Exceptions\StateIsNotDefined;

class OpenWeatherApi implements WeatherApiInterface
{
    private string $baseUrl;

    function __construct(private string $apiKey)
    {
        $this->baseUrl = env("OPEN_WEATHER_MAP_API_URL");
    }

    function getWeather(
        string $city,
        string $state,
        string $country,
        string $customResponseTransformer = null
    ): OpenWeatherResponse {
        if (empty($city)) {
            throw new CityIsNotDefined();
        }

        if (empty($state)) {
            throw new StateIsNotDefined();
        }

        if (empty($country)) {
            throw new CountryIsNotDefined();
        }

        /**
         * @var Response
         */
        $response = $this->get("/weather?q={$city},{$state},{$country}");

        if ($response->status() === 404) {
            throw new CityNotFound();
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
