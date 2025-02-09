<?php

declare(strict_types=1);

namespace Interfaces;

interface ResponseInterface
{
    public function sendResponse(int $status_code, string $message, array|object $data = []): void;
    public function responseWithSuccess(int $status_code): bool;
}
