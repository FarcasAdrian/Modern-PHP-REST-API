<?php

declare(strict_types=1);

namespace Classes;

use Enums\HttpStatusCodeEnum;
use Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @param int $status_code
     * @param string $message
     * @param array|object $data
     * @return void
     */
    public function sendResponse(int $status_code, string $message, array|object $data = []): void
    {
        http_response_code($status_code);
        $json = json_encode([
            'status_code' => $status_code,
            'success' => $this->responseWithSuccess($status_code),
            'message' => $message,
            'data' => $data,
        ]);

        if ($json === false) {
            http_response_code(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value);
            $json = json_encode([
                'status_code' => HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value,
                'success' => $this->responseWithSuccess(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value),
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
        return HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value <= $status_code && $status_code <= HttpStatusCodeEnum::MISCELLANEOUS_PERSISTENT_WARNING->value;
    }
}
