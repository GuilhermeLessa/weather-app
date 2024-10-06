import axios from "axios";
import type HttpClient from "../../application/http/HttpClient";
import { Unauthorized, UnprocessableEntity, InternalServerError, HttpErrorResponse, HttpSuccesResponse, OK, NoContent } from "../../application/http/HttpClient";
import { left, right, type Either } from "@/domain/shared/Either";

export default class AxiosHttp implements HttpClient {

    private client: any;

    constructor(
        private baseUrl: string,
        options: { withCredentials: boolean, withXSRFToken: boolean },
    ) {
        const { withCredentials, withXSRFToken } = options;
        this.client = axios.create({
            baseURL: this.baseUrl,
            withCredentials,
            withXSRFToken
        });
    }

    get(url: string, options?: { params: any }): Promise<
        Either<
            Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
            OK | NoContent | HttpSuccesResponse
        >
    > {
        return this.request('get', url, options);
    }

    post(url: string, body?: any, options?: { params: any }): Promise<
        Either<
            Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
            OK | NoContent | HttpSuccesResponse
        >
    > {
        return this.request('post', url, body, options);
    }

    delete(url: string, options?: { params: any }): Promise<
        Either<
            Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
            OK | NoContent | HttpSuccesResponse
        >
    > {
        return this.request('delete', url, options);
    }

    private async request(method: string, url: string, body?: any, options?: { params: any }): Promise<
        Either<
            Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
            OK | NoContent | HttpSuccesResponse
        >
    > {
        let response: any;

        try {
            response = await this.client.request({
                baseURL: this.baseUrl,
                url,
                method,
                headers: { "Content-Type": "application/json" },
                params: options?.params,
                data: body,
                responseType: 'json',
            });
            return this.handleSuccessResponse(response);

        } catch (error: any) {
            return this.handleErrorResponse(error);
        }
    }

    private handleSuccessResponse(response: any): Either<
        HttpErrorResponse,
        OK | NoContent | HttpSuccesResponse
    > {
        if (response?.status == OK.STATUS_CODE) {
            return right(new OK(response.data));
        }

        if (response?.status == NoContent.STATUS_CODE) {
            return right(new NoContent());
        }

        return right(new HttpSuccesResponse(response.data));
    }

    private handleErrorResponse(error: any): Either<
        Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
        HttpSuccesResponse
    > {
        const message = error?.response?.data?.message || error.message;

        if (error?.response?.status == Unauthorized.STATUS_CODE) {
            return left(new Unauthorized(message));
        }

        if (error?.response?.status == UnprocessableEntity.STATUS_CODE) {
            return left(new UnprocessableEntity(message));
        }

        if (error?.response?.status == InternalServerError.STATUS_CODE) {
            return left(new InternalServerError(message));
        }

        return left(new HttpErrorResponse(message));
    }

}