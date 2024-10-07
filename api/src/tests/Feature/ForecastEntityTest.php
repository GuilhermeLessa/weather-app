<?php

namespace Tests\Feature;

use App\Api\WeatherApi\Exceptions\MalformedResponseData;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherApi;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherIcons;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse;
use App\Domain\Entities\Forecast;
use App\Domain\Exceptions\CityIsNotDefined;
use App\Domain\Exceptions\CountryIsNotDefined;
use App\Domain\Exceptions\MaximumForecastReached;
use App\Models\ForecastModel;
use App\Repositories\ForecastRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ForecastEntityTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_forecast_searching(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();
        $forecastModel = $forecast->getModelSaved();

        $this->assertInstanceOf(OpenWeatherResponse::class, $weatherResponse);
        $this->assertInstanceOf(ForecastModel::class, $forecastModel);
    }

    public function test_max_3_forecast_records_per_user(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast1 = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $forecast2 = new Forecast($weatherApi, $forecastRepository, "Colorado", "US");
        $forecast3 = new Forecast($weatherApi, $forecastRepository, "Milwaukee", "US");

        $this->expectException(MaximumForecastReached::class);
        $forecast4 = new Forecast($weatherApi, $forecastRepository, "Rio de Janeiro", "BR");
    }

    public function test_city_name_required(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $this->expectException(CityIsNotDefined::class);
        $forecast = new Forecast($weatherApi, $forecastRepository, "", "US");
    }

    public function test_country_name_required(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $this->expectException(CountryIsNotDefined::class);
        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "");
    }

    public function test_saved_forecast_valid_types(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $forecastModel = $forecast->getModelSaved();

        $data = [
            'id' => $forecastModel->id,
            'uuid' => $forecastModel->uuid,
            'user_id' => $forecastModel->user_id,
            'city' => $forecastModel->city,
            'country' => $forecastModel->country,
            'weather_data' => $forecastModel->weather_data,
            'created_at' => $forecastModel->created_at,
            'updated_at' => $forecastModel->updated_at,
        ];

        $validator = Validator::make($data, [
            'id' => 'integer',
            'uuid' => 'uuid',
            'user_id' => 'integer',
            'city' => 'string',
            'country' => 'string',
            'weather_data' => 'json',
            'created_at' => 'date',
            'updated_at' => 'date'
        ]);

        $this->assertEquals(count($validator->validate()), count($data));
    }

    public function test_saved_forecast_valid_content(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $city = "New York";
        $country = "US";
        $forecast = new Forecast($weatherApi, $forecastRepository, $city, $country);
        $forecastModel = $forecast->getModelSaved();

        $this->assertEquals($forecastModel->user_id, $testerUser->id);
        $this->assertEquals($forecastModel->city, $city);
        $this->assertEquals($forecastModel->country, $country);
        $this->assertEquals($forecastModel->active, true);
    }

    public function test_weather_response_transformation(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();

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
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();
        $transformedData = $weatherResponse->toArray();

        $validator = Validator::make($transformedData, [
            'city' => 'string',
            'country' => 'string',
            'description' => 'string',
            'icon' => 'string',
            'temperature' => 'decimal:2',
            'minimumTemperature' => 'decimal:2',
            'maximumTemperature' => 'decimal:2',
            'humidity' => 'integer',
            'wind' => 'decimal:2'
        ]);

        $this->assertEquals(count($validator->validate()), count($transformedData));
    }

    public function test_weather_response_get_data(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();

        $originalData = $weatherResponse->getData();
        $this->assertIsArray($originalData);
    }

    public function test_weather_response_to_array(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();

        $transformedData = $weatherResponse->toArray();
        $this->assertIsArray($transformedData);
    }

    public function test_weather_response_get_city(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();
        $city = $weatherResponse->getCity();

        $this->assertIsString($city);
        $this->assertEquals($city, $originalData['name']);
        $this->assertEquals($city, $transformedData['city']);
    }

    public function test_weather_response_get_country(): void
    {
        $testerUser = User::factory()->create();
        $weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $forecastRepository = new ForecastRepository($testerUser->id);

        $forecast = new Forecast($weatherApi, $forecastRepository, "New York", "US");
        $weatherResponse = $forecast->getWheaterResponse();

        $originalData = $weatherResponse->getData();
        $transformedData = $weatherResponse->toArray();
        $country = $weatherResponse->getCountry();

        $this->assertIsString($country);
        $this->assertEquals($country, $originalData['sys']['country']);
        $this->assertEquals($country, $transformedData['country']);
    }

    public function test_weather_response_malformed(): void
    {
        $this->expectException(MalformedResponseData::class);
        $weatherResponse = new OpenWeatherResponse([]);
    }
}
