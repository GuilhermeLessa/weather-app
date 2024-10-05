<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

use App\Api\WeatherApi\Interfaces\WeatherResponseInterface;
use App\Repositories\Interfaces\ForecastRepositoryInterface;
use App\Models\ForecastModel;
use Illuminate\Database\Eloquent\Collection;

class ForecastRepository implements ForecastRepositoryInterface
{

    public function countActives(): int
    {
        return ForecastModel::where('user_id', Auth::id())
            ->where('active', true)
            ->count();
    }

    /*public function hasSomeActive(string $city, string $country): bool
    {
        return ForecastModel::where('user_id', Auth::id())
            ->where('city', $city)
            ->where('country', $country)
            ->where('active', true)
            ->count() > 0;
    }*/

    public function inactivateAll(string $city, string $country): void
    {
        ForecastModel::where('user_id', Auth::id())
            ->where('city', $city)
            ->where('country', $country)
            ->where('active', true)
            ->update(['active' => false]);
    }

    public function findFirst(string $uuid): ForecastModel | null
    {
        return ForecastModel::where('user_id', Auth::id())
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * @return ForecastModel[]
     */
    public function findAllActives(): Collection
    {
        return ForecastModel::where('user_id', Auth::id())
            ->where('active', true)
            ->orderByDesc('id')
            ->get();
    }

    public function save(WeatherResponseInterface $weatherResponse): ForecastModel
    {
        $forecast = new ForecastModel();
        $forecast->user_id = Auth::id();
        $forecast->city = $weatherResponse->getCity();
        $forecast->country = $weatherResponse->getCountry();
        $forecast->weather_data = $weatherResponse->getData();
        $forecast->active = true;
        $forecast->save();
        return $forecast;
    }
}
