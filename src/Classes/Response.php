<?php

declare(strict_types=1);

namespace Classes;

class Response
{
    const SUCCESS_STATUS_CODE = 200;
    const CLIENT_ERROR_STATUS_CODE = 400;
    const UNAUTHORIZED_STATUS_CODE = 401;
    const NOT_FOUND_STATUS_CODE = 404;
    const METHOD_NOT_ALLOWED_STATUS_CODE = 405;
    const INTERNAL_SERVER_ERROR_STATUS_CODE = 500;

    /**
     * @param int $status_code
     * @param string $message
     * @param array $data
     * @return void
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
            http_response_code(self::INTERNAL_SERVER_ERROR_STATUS_CODE);
            $json = json_encode([
               'status_code' => self::INTERNAL_SERVER_ERROR_STATUS_CODE,
               'success' => $this->responseWithSuccess(self::INTERNAL_SERVER_ERROR_STATUS_CODE),
                'message' => 'Failed to encode the response.',
            ]);
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
