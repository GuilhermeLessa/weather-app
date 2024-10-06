import type UseCase from "./UseCase";
import { left, right, type Either } from "../../domain/shared/Either";
import type WeatherApiInterface from "../weather-api/WeatherApiInterface";
import type { HttpErrorResponse, Unauthorized } from "../http/HttpClient";

export default class Logout implements UseCase {

    constructor(
        private weatherApi: WeatherApiInterface,
    ) { }

    async execute(): LogoutOutput {
        const logoutResult = await this.weatherApi.logout();
        if (logoutResult.isRight()) {
            return right("Unauthenticated");
        }

        return left(logoutResult.value);
    }
}

export type LogoutOutput = Promise<
    Either<
        Unauthorized | HttpErrorResponse,
        "Unauthenticated"
    >
>;
