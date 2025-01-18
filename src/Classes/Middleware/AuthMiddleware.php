<?php

declare(strict_types=1);

namespace Classes\Middleware;

use Classes\Request;
use Classes\Response;
use Services\AuthenticationService;
use Exception;
use Enums\HttpStatusCodeEnum;

class AuthMiddleware
{
    public function __construct(private AuthenticationService $authentication_service, private Response $response) {}

    /**
     * @param Request $request
     * @return \stdClass|null
     */
    public function handle(Request $request): ?\stdClass
    {
        $auth_header = $request->getHeader('Authorization');
        $matches = '';

        if (!$auth_header || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            $this->response->sendResponse(HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value, 'Not authorized.');
            exit;
        }

        try {
            $jwt = $matches[1];
            return $this->authentication_service->decodeToken($jwt);
        } catch (Exception $exception) {
            $this->response->sendResponse(
                HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value,
                'Invalid token: ' . $exception->getMessage()
            );
            exit;
        }
    }
}
