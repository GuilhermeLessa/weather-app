import { right, type Either, left } from "@/domain/shared/Either";
import type UseCase from "./UseCase";
import type WeatherApiInterface from "../weather-api/WeatherApiInterface";

export default class CheckIsAuthenticated implements UseCase {

    constructor(
        private weatherApi: WeatherApiInterface
    ) { }

    async execute(): CheckIsAuthenticatedOutput {
        const authenticatedOrNot = await this.weatherApi.isAuthenticated();
        if (authenticatedOrNot.isRight()) {
            return right(true);
        }

        return left(false);
    }
}

export type CheckIsAuthenticatedOutput = Promise<
    Either<
        false,
        true
    >
>;
