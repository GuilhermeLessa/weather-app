<?php

namespace Tests\Feature\WeatherApi\OpenWeatherApi;

use App\Api\WeatherApi\Exceptions\CityIsNotDefined;
use App\Api\WeatherApi\Exceptions\CountryIsNotDefined;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherApi;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherIcons;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Validator;

class OpenWeatherApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_forecast_searching(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");

        $this->assertInstanceOf(OpenWeatherResponse::class, $weatherResponse);
    }

    public function test_a_new_forecast_searching_with_transformer(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather(
            "New York",
            "US",
            "App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse"
        );

        $this->assertInstanceOf(OpenWeatherResponse::class, $weatherResponse);
    }

    public function test_city_name_required(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));

        $this->expectException(CityIsNotDefined::class);
        $weatherApi->getWeather("", "US");
    }

    public function test_country_name_required(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));

        $this->expectException(CountryIsNotDefined::class);
        $weatherApi->getWeather("New York", "");
    }

    public function test_weather_response_get_data(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");

        $originalData = $weatherResponse->getData();
        $this->assertIsArray($originalData);
    }

    public function test_weather_response_to_array(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");

        $transformedData = $weatherResponse->toArray();
        $this->assertIsArray($transformedData);
    }

    public function test_weather_response_transformation(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");

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
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");
        $transformedData = $weatherResponse->toArray();

        $validator = Validator::make($transformedData, [
            'city' => 'string',
            'country' => 'string',
            'description' => 'string',
            'icon' => 'url',
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
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();
        $city = $weatherResponse->getCity();

        $this->assertIsString($city);
        $this->assertEquals($city, $originalData['name']);
        $this->assertEquals($city, $transformedData['city']);
    }

    public function test_weather_response_get_country(): void
    {
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $weatherResponse = $weatherApi->getWeather("New York", "US");

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();
        $country = $weatherResponse->getCountry();

        $this->assertIsString($country);
        $this->assertEquals($country, $originalData['sys']['country']);
        $this->assertEquals($country, $transformedData['country']);
    }
}
