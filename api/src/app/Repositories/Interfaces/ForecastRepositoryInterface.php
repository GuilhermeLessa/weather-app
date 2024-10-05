<?php

namespace App\Repositories\Interfaces;

use App\Api\WeatherApi\Interfaces\WeatherResponseInterface;
use App\Models\ForecastModel;
use Illuminate\Database\Eloquent\Collection;

interface ForecastRepositoryInterface
{
    public function countActives(): int;
    public function inactivateAll(string $city, string $country): void;
    public function findFirst(string $uuid): ForecastModel | null;
    
    /**
     * @return ForecastModel[]
     */
    public function findAllActives(): Collection;
    
    public function save(WeatherResponseInterface $weatherResponse): ForecastModel;
}
