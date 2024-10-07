<?php

namespace Tests\Feature\WeatherApi\OpenWeatherApi;

use Illuminate\Support\Facades\Http;
use App\Api\WeatherApi\Exceptions\MalformedResponseData;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherIcons;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Validator;

class OpenWeatherResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_forecast_searching(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());
        $this->assertInstanceOf(OpenWeatherResponse::class, $weatherResponse);
    }

    public function test_weather_response_malformed(): void
    {
        $this->expectException(MalformedResponseData::class);
        $weatherResponse = new OpenWeatherResponse([]);
    }

    public function test_weather_response_get_data(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());

        $originalData = $weatherResponse->getData();
        $this->assertIsArray($originalData);
    }

    public function test_weather_response_to_array(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());

        $transformedData = $weatherResponse->toArray();
        $this->assertIsArray($transformedData);
    }

    public function test_weather_response_transformation(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();

        $this->assertEquals(count($transformedData), 9);
        $this->assertEquals($transformedData['city'], $originalData['name']);
        $this->assertEquals($transformedData['country'], $originalData['sys']['country']);
        $this->assertEquals($transformedData['description'], $originalData['weather'][0]['description']);
        $this->assertEquals($transformedData['icon'], OpenWeatherIcons::url($originalData['weather'][0]['icon']));
        $this->assertEquals($transformedData['temperature'], $originalData['main']['temp']);
        $this->assertEquals($transformedData['minimumTemperature'], $originalData['main']['temp_min']);
        $this->assertEquals($transformedData['maximumTemperature'], $originalData['main']['temp_max']);
        $this->assertEquals($transformedData['humidity'], $originalData['main']['humidity']);
        $this->assertEquals($transformedData['wind'], $originalData['wind']['speed']);
    }

    public function test_weather_response_transformation_types(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());
        $transformedData = $weatherResponse->toArray();

        $validator = Validator::make($transformedData, [
            'city' => 'string',
            'country' => 'string',
            'description' => 'string',
            'icon' => 'string',
            'temperature' => 'decimal:0,2',
            'minimumTemperature' => 'decimal:0,2',
            'maximumTemperature' => 'decimal:0,2',
            'humidity' => 'integer',
            'wind' => 'decimal:0,2'
        ]);

        $this->assertEquals(count($validator->validate()), count($transformedData));
    }

    public function test_weather_response_get_city(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();
        $city = $weatherResponse->getCity();

        $this->assertIsString($city);
        $this->assertEquals($city, $originalData['name']);
        $this->assertEquals($city, $transformedData['city']);
    }

    public function test_weather_response_get_country(): void
    {
        $response = $this->getWeather("New York", "US");
        $weatherResponse = new OpenWeatherResponse($response->json());

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();
        $country = $weatherResponse->getCountry();

        $this->assertIsString($country);
        $this->assertEquals($country, $originalData['sys']['country']);
        $this->assertEquals($country, $transformedData['country']);
    }

    private function getWeather(string $city, string $country)
    {
        $apiKey = env("OPEN_WEATHER_MAP_API_KEY");
        $baseUrl = env("OPEN_WEATHER_MAP_API_URL");
        $url = "/weather?q={$city},{$country}";
        return Http::get("{$baseUrl}{$url}&appid={$apiKey}&units=imperial");
    }
    
}
