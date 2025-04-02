export const STATUS_CODES = {
    SUCCESS: 200,
    BAD_REQUEST: 400,
    UNAUTHORIZED: 401,
    FORBIDDEN: 403,
    NOT_FOUND: 404,
};

export const isSuccess = (code) => code === STATUS_CODES.SUCCESS;
export const isUnauthorized = (code) => code === STATUS_CODES.UNAUTHORIZED;
export const isForbidden = (code) => code === STATUS_CODES.FORBIDDEN;
