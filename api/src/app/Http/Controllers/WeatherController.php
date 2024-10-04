<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Api\WeatherApi\OpenWeatherApi\Exceptions\CityNotFound;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherApi;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse;

class WeatherController extends Controller
{
    private OpenWeatherApi $api;

    function __construct()
    {
        /**
         * @var OpenWeatherApi
         */
        $this->api = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
    }

    public function find(Request $request): JsonResponse
    {
        $request->validate([
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        try {
            $city = $request->get('city');
            $state = $request->get('state');
            $country = $request->get('country');

            /**
             * @var OpenWeatherResponse
             */
            $response = $this->api->getWeather(
                $city,
                $state,
                $country,
                "App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse"
            );

            /*
                ID
                user_id
                city, 
                state, 
                country,
                api_forecast_data,
                excluded
                datetime            
            */

            return response()->json($response->toArray());
        } catch (CityNotFound $e) {
            return response()->json([
                "message" => "City not found"
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Error loading forecast"
            ], 500);
        }
    }
}
