<?php

namespace Classes;

class Response
{
    const SUCCESS_STATUS_CODE = 200;
    const CLIENT_ERROR_STATUS_CODE = 400;
    const NOT_FOUND_STATUS_CODE = 404;
    const METHOD_NOT_ALLOWED_STATUS_CODE = 405;
    const INTERNAL_SERVER_ERROR_STATUS_CODE = 500;

    public function sendResponse(int $status_code, array $data): void
    {
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }

    public function responseWithSuccess(int $status_code)
    {
        return 200 <= $status_code && $status_code <= 299;
    }
}
