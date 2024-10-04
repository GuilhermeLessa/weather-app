<?php

namespace App\Api\WeatherApi\OpenWeatherApi\Exceptions;

use Exception;

class CityIsNotDefined extends Exception {}
class StateIsNotDefined extends Exception {}
class CountryIsNotDefined extends Exception {}
class CityNotFound extends Exception {}
class MalformedForecastData extends Exception {}
