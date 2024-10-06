import type UseCase from "./UseCase";
import { left, right, type Either } from "../../domain/shared/Either";
import type WeatherApiInterface from "../weather-api/WeatherApiInterface";
import DomainException from "@/domain/shared/DomainExceptions";
import type { HttpErrorResponse, Unauthorized } from "../http/HttpClient";

export default class Login implements UseCase {

    constructor(
        private weatherApi: WeatherApiInterface,
    ) { }

    async execute(input: LoginInput): LoginOutput {
        const { email, password } = input;

        if (!email) {
            return left(new DomainException("Email is required."));
        }

        if (!password) {
            return left(new DomainException("Password is required."));
        }

        const authenticatedOrUnauthorized = await this.weatherApi.login(email, password);
        if (authenticatedOrUnauthorized.isRight()) {
            return right("Authenticated");
        }

        const error = authenticatedOrUnauthorized.value;
        return left(error);
    }
}

export type LoginInput = {
    email: string, password: string
};

export type LoginOutput = Promise<
    Either<
        DomainException | Unauthorized | HttpErrorResponse,
        "Authenticated"
    >
>;
