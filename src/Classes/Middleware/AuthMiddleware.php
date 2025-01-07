<?php

declare(strict_types=1);

namespace Classes\Middleware;

use Classes\Request;
use Classes\Response;
use Services\AuthenticationService;
use Exception;

class AuthMiddleware
{
    private AuthenticationService $authentication_service;
    private Response $response;

    public function __construct(AuthenticationService $authentication_service, Response $response)
    {
        $this->authentication_service = $authentication_service;
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @return \stdClass|null
     */
    public function handle(Request $request): ?\stdClass
    {
        $auth_header = $request->getHeader('Authorization');
        $matches = '';

        if (!$auth_header || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            $this->response->sendResponse(Response::UNAUTHORIZED_STATUS_CODE, 'Not authorized.');
            exit;
        }

        try {
            $jwt = $matches[1];
            return $this->authentication_service->decodeToken($jwt);
        } catch (Exception $exception) {
            $this->response->sendResponse(
                Response::UNAUTHORIZED_STATUS_CODE,
                'Invalid token: ' . $exception->getMessage()
            );
            exit;
        }
    }
}
