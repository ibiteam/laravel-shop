export const STATUS_CODES = {
    SUCCESS: 200,
    BAD_REQUEST: 400,
    UNAUTHORIZED: 401,
    FORBIDDEN: 403,
    NOT_FOUND: 404,
};

const isSuccess = (code) => code === STATUS_CODES.SUCCESS;
const isUnauthorized = (code) => code === STATUS_CODES.UNAUTHORIZED;
const isForbidden = (code) => code === STATUS_CODES.FORBIDDEN;

export default {
    isSuccess,
    isUnauthorized,
    isForbidden,
}
