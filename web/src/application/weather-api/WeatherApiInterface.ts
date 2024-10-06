import type { Either } from "@/application/shared/Either";
import type { HttpErrorResponse, InternalServerError, NoContent, OK, Unauthorized } from "../http/HttpClient";

export default interface WeatherApiInterface {

    login(email: string, password: string): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            NoContent
        >
    >;

    logout(): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            NoContent
        >
    >;

    isAuthenticated(): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            NoContent
        >
    >;

    getAuthenticatedUser(): Promise<
        Either<
            Unauthorized | HttpErrorResponse,
            OK
        >
    >;

    findForecast(city: string, country: string): Promise<
        Either<
            Unauthorized | InternalServerError | HttpErrorResponse,
            OK
        >
    >;

    listForecast(): Promise<
        Either<
            Unauthorized | InternalServerError | HttpErrorResponse,
            OK
        >
    >;

    inactivateForecast(uuid: string): Promise<
        Either<
            Unauthorized | InternalServerError | HttpErrorResponse,
            NoContent
        >
    >;

}