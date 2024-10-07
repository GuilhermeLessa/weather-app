<?php

namespace Tests\Resources;

class FakeWeatherResponseData
{

    public static function get(string $city = "New York", string $country = "US"): array
    {
        return [
            'name' => $city,
            'sys' => [
                'country' => $country
            ],
            'weather' => [
                0 => [
                    'description' => 'clear sky',
                    'icon' => '01d'
                ]
            ],
            'main' => [
                'temp' => 298.86,
                'temp_min' => 298.09,
                'temp_max' => 300.07,
                "humidity" => 49
            ],
            'wind' => [
                'speed' => 2.57
            ]
        ];
    }
}
