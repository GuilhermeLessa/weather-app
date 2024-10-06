import type UseCase from "./UseCase";
import { left, right, type Either } from "../shared/Either";
import type WeatherApiInterface from "../weather-api/WeatherApiInterface";
import type { HttpErrorResponse, InternalServerError, Unauthorized } from "../http/HttpClient";

export default class InactivateForecast implements UseCase {

    constructor(
        private weatherApi: WeatherApiInterface,
    ) { }

    async execute(input: InactivateForecastInput): InactivateForecastOutput {
        const { uuid } = input;

        const inactivateOrError = await this.weatherApi.inactivateForecast(uuid);
        if (inactivateOrError.isRight()) {
            return right("done");
        }

        return left(inactivateOrError.value);
    }
}

export type InactivateForecastInput = {
    uuid: string
};

export type InactivateForecastOutput = Promise<
    Either<
        Unauthorized | InternalServerError | HttpErrorResponse,
        "done"
    >
>;
