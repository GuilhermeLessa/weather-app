<?php

namespace App\Api\WeatherApi;

interface WeatherResponseInterface
{
    public function getData(): mixed;
    public function toArray(): array;
}
