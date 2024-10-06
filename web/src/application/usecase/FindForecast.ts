import { right, type Either, left } from "@/application/shared/Either";
import type UseCase from "./UseCase";
import type WeatherApiInterface from "../weather-api/WeatherApiInterface";
import ApplicationError from "../exceptions/ApplicationError";
import DomainException from "../exceptions/DomainExceptions";

export default class FindForecast implements UseCase {

    constructor(
        private weatherApi: WeatherApiInterface
    ) { }

    async execute(input: FindForecastInput): FindForecastOutput {
        const { city, country } = input;

        if (!city) {
            return left(new DomainException("Please enter a city name."));
        }

        if (!country) {
            return left(new DomainException("Please select a country."));
        }

        const forecastDataOrError = await this.weatherApi.findForecast(city, country);
        if (forecastDataOrError.isLeft()) {
            const error = forecastDataOrError.value;
            return left(new ApplicationError(error.message || "Error searching for a forecast."));
        }

        const forecast = forecastDataOrError.value
        return right(forecast.data);
    }
}

export type FindForecastInput = {
    city: string, country: string
};

export type FindForecastOutput = Promise<
    Either<
        DomainException | ApplicationError,
        any
    >
>;
