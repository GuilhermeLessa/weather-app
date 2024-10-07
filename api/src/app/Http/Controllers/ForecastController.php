<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

use App\Api\WeatherApi\Exceptions\Base\WeatherApiException;
use App\Api\WeatherApi\Exceptions\CityNotFound;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherApi;
use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse;
use App\Repositories\ForecastRepository;
use App\Domain\Entities\Forecast;
use App\Domain\Exceptions\Base\DomainException;
use App\Models\ForecastModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ForecastController extends Controller
{
    private OpenWeatherApi $weatherApi;
    private ForecastRepository $forecastRepository;

    function __construct()
    {
        $this->weatherApi = new OpenWeatherApi(env("OPEN_WEATHER_MAP_API_KEY"));
        $this->forecastRepository = new ForecastRepository();
    }

    public function list(Request $request): JsonResponse
    {
        try {
            /**
             * @return ForecastModel[]
             */
            $forecasts = $this->forecastRepository->findAllActives();

            $list = ((object) $forecasts)->map(fn($forecast) => [
                'uuid' => $forecast->uuid,
                'created_at' => $forecast->created_at,
                ...(new OpenWeatherResponse($forecast->weather_data))->toArray()
            ]);

            return response()->json($list);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Error listing forecasts."
            ], 500);
        }
    }

    public function find(Request $request): JsonResponse
    {
        $request->validate([
            'city' => 'required',
            'country' => 'required',
        ]);
        $city = $request->get('city');
        $country = $request->get('country');

        try {
            $forecast = new Forecast(
                $this->weatherApi,
                $this->forecastRepository,
                $city,
                $country,
                "App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse"
            );

            /**
             * @var ForecastModel
             */
            $forecastModel = $forecast->getModelSaved();

            /**
             * @var OpenWeatherResponse
             */
            $weatherResponse = $forecast->getWheaterResponse();

            return response()->json([
                'uuid' => $forecastModel->uuid,
                'created_at' => $forecastModel->created_at,
                ...$weatherResponse->toArray()
            ]);
        } catch (CityNotFound $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 404);
        } catch (WeatherApiException $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        } catch (DomainException $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Error loading forecast."
            ], 500);
        }
    }

    public function inactivate(Request $request, string $uuid): Response | JsonResponse
    {
        try {
            $validator = Validator::make(['uuid' => $uuid], [
                'uuid' => 'uuid|required',
            ]);

            if ($validator->fails()) {
                throw new Exception('valid uuid is required.');
            }

            /**
             * @var ForecastModel
             */
            $forecastModel = $this->forecastRepository->findFirst($uuid);

            if (!$forecastModel) {
                return response()->noContent();
            }

            $this->forecastRepository
                ->inactivateAll($forecastModel->city, $forecastModel->country);

            return response()->noContent();
        } catch (Exception $e) {
            return response()->json([
                "message" => "Error inactivating forecast."
            ], 500);
        }
    }
}
