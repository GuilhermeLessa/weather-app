<?php

namespace App\Domain\Entities;

use App\Api\WeatherApi\Interfaces\WeatherApiInterface;
use App\Api\WeatherApi\Interfaces\WeatherResponseInterface;
use App\Repositories\Interfaces\ForecastRepositoryInterface;
use App\Models\ForecastModel;
use App\Domain\Exceptions\CityIsNotDefined;
use App\Domain\Exceptions\CountryIsNotDefined;
use App\Domain\Exceptions\MaximumForecastReached;

class Forecast
{
    private WeatherResponseInterface $wheaterResponse;
    private ForecastModel $modelSaved;

    const MAX_FORECAST_CACHE_LENGTH = 3;

    function __construct(
        private WeatherApiInterface $weatherApi,
        private ForecastRepositoryInterface $forecastRepository,
        private string $city,
        private string $country,
        private string | null $customWeatherResponseTransformer = null,
    ) {
        $this->validate();

        $this->wheaterResponse = $this->weatherApi->getWeather(
            $this->city,
            $this->country,
            $this->customWeatherResponseTransformer,
        );

        $this->equalizeSearchTerms();

        $this->forecastRepository->inactivateAll($this->city, $this->country);

        $this->modelSaved = $this->forecastRepository->save($this->wheaterResponse);
    }

    public function getWheaterResponse(): WeatherResponseInterface
    {
        return $this->wheaterResponse;
    }

    public function getModelSaved(): ForecastModel
    {
        return $this->modelSaved;
    }

    private function validate()
    {
        if ($this->forecastRepository->countActives() >= self::MAX_FORECAST_CACHE_LENGTH) {
            throw new MaximumForecastReached("Maximum number of forecast reached.");
        }

        if (empty($this->city)) {
            throw new CityIsNotDefined("City is not defined.");
        }

        if (empty($this->country)) {
            throw new CountryIsNotDefined("Country is not defined.");
        }
    }

    private function equalizeSearchTerms(): void
    {
        $this->city = $this->wheaterResponse->getCity();
        $this->country = $this->wheaterResponse->getCountry();
    }
}
