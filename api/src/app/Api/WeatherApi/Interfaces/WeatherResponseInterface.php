<?php

namespace App\Api\WeatherApi\Interfaces;

interface WeatherResponseInterface
{
    public function getCity(): string;
    public function getCountry(): string;
    public function getData(): array;
    public function toArray(): array;
}
