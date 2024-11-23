<?php

namespace Classes;

use HttpResponseException;

class Response
{
    const SUCCESS_STATUS_CODE = 200;
    const CLIENT_ERROR_STATUS_CODE = 400;
    const NOT_FOUND_STATUS_CODE = 404;
    const METHOD_NOT_ALLOWED_STATUS_CODE = 405;
    const INTERNAL_SERVER_ERROR_STATUS_CODE = 500;

    /**
     * @param int $status_code
     * @param string $message
     * @param array $data
     * @return void
     * @throws HttpResponseException
     */
    public function sendResponse(int $status_code, string $message, array $data = []): void
    {
        http_response_code($status_code);
        $json = json_encode([
            'status_code' => $status_code,
            'success' => $this->responseWithSuccess($status_code),
            'message' => $message,
            'data' => $data,
        ]);

        if ($json === false) {
            throw new HttpResponseException('Failed to encode the response.', self::INTERNAL_SERVER_ERROR_STATUS_CODE);
        }

        echo $json;
    }

    /**
     * @param int $status_code
     * @return bool
     */
    public function responseWithSuccess(int $status_code): bool
    {
        return 200 <= $status_code && $status_code <= 299;
    }
}
