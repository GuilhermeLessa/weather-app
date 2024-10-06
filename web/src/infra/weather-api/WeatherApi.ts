import type WeatherApiInterface from "../../application/weather-api/WeatherApiInterface";
import type HttpClient from "@/application/http/HttpClient";
import { HttpErrorResponse, InternalServerError, NoContent, OK, Unauthorized, UnprocessableEntity, type HttpSuccesResponse } from "@/application/http/HttpClient";
import { type Either } from "@/application/shared/Either";

export default class WeatherApi implements WeatherApiInterface {

    private constructor(
        private httpClient: HttpClient,
    ) { }

    static async create(
        httpClient: HttpClient,
    ): Promise<WeatherApi | void> {
        const response = await httpClient.get("/sanctum/csrf-cookie");
        if (response.isRight()) {
            return new WeatherApi(httpClient);
        }
    }

    login(email: string, password: string): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            NoContent
        >
    > {
        return this.httpClient.post("/login", { email, password });
    }

    logout(): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            NoContent
        >
    > {
        return this.httpClient.post("/logout");
    }

    isAuthenticated(): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            NoContent
        >
    > {
        return this.httpClient.get("/authenticated");
    }

    getAuthenticatedUser(): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            OK
        >
    > {
        return this.httpClient.get("/api/user");
    }

    findForecast(city: string, country: string): Promise<
        Either<
            Unauthorized | InternalServerError | HttpErrorResponse,
            OK
        >
    > {
        return this.httpClient.get("/api/forecast", { params: { city, country } });
    }

    listForecast(): Promise<
        Either<
            Unauthorized | InternalServerError | HttpErrorResponse,
            OK
        >
    > {
        return this.httpClient.get("/api/forecast/list");
    }

    inactivateForecast(uuid: string): Promise<
        Either<
            Unauthorized | InternalServerError | HttpErrorResponse,
            NoContent
        >
    > {
        return this.httpClient.delete(`/api/forecast/${uuid}`);
    }

}
