import { right, type Either, left } from "@/application/shared/Either";
import type UseCase from "./UseCase";
import type WeatherApiInterface from "../weather-api/WeatherApiInterface";
import ApplicationError from "../exceptions/ApplicationError";

export default class ListForecast implements UseCase {

    constructor(
        private weatherApi: WeatherApiInterface
    ) { }

    async execute(): ListForecastOutput {
        const listForecastOrError = await this.weatherApi.listForecast();
        if (listForecastOrError.isRight()) {
            const list = listForecastOrError.value
            return right(list.data || []);
        }

        const error = listForecastOrError.value;
        return left(new ApplicationError(error.message || "Error loading forecast list."));
    }
}

export type ListForecastOutput = Promise<
    Either<
        ApplicationError,
        []
    >
>;
