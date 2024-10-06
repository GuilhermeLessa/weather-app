import type { Either } from "@/domain/shared/Either";

export default interface HttpClient {
    get(url: string, options?: any)
        : Promise<
            Either<
                Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
                OK | NoContent | HttpSuccesResponse
            >
        >;

    post(url: string, body?: any, options?: any)
        : Promise<
            Either<
                Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
                OK | NoContent | HttpSuccesResponse
            >
        >;

    delete(url: string, options?: any): Promise<
        Either<
            Unauthorized | UnprocessableEntity | InternalServerError | HttpErrorResponse,
            OK | NoContent | HttpSuccesResponse
        >
    >;
}

abstract class AbstractHttpResponse {
    static readonly STATUS_CODE: number | null = null;

    constructor() {
        if (!AbstractHttpResponse.STATUS_CODE == undefined) {
            throw new Error("STATUS_CODE is not defined");
        }
    }
}

abstract class AbstractHttpSuccesResponse extends AbstractHttpResponse {
    static readonly STATUS_CODE: number | null = null;
    constructor(readonly data?: any) {
        super();
    }
}

abstract class AbstractHttpErrorResponse extends AbstractHttpResponse {
    static readonly STATUS_CODE: number | null = null;
    constructor(readonly message?: string) {
        super();
    }
}

//http successes
export class HttpSuccesResponse extends AbstractHttpSuccesResponse {
    static readonly STATUS_CODE: number | null = null;
}
export class OK extends HttpSuccesResponse {
    static readonly STATUS_CODE: number = 200;
}
export class NoContent extends HttpSuccesResponse {
    static readonly STATUS_CODE: number = 204;
    constructor() { //no content response has no data attribute
        super();
    }
}

//http errors
export class HttpErrorResponse extends AbstractHttpErrorResponse {
    static readonly STATUS_CODE: number | null = null;
}
export class Unauthorized extends HttpErrorResponse {
    static readonly STATUS_CODE: number = 401;
}
export class UnprocessableEntity extends HttpErrorResponse {
    static readonly STATUS_CODE: number = 422;
}
export class InternalServerError extends HttpErrorResponse {
    static readonly STATUS_CODE: number = 500;
}