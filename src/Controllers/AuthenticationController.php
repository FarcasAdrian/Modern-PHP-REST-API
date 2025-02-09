<?php

declare(strict_types=1);

namespace Controllers;

use Classes\Request;
use Services\AuthenticationService;
use Exception;
use Enums\HttpStatusCodeEnum;
use Interfaces\RedisHandlerInterface;
use Interfaces\ResponseInterface;

class AuthenticationController extends Controller
{
    public function __construct(
        private AuthenticationService $authentication_service,
        private ResponseInterface $response,
        private Request $request,
        private RedisHandlerInterface $redisHandler
    ) {}

    /**
     * @return void
     */
    public function login(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
                'Method not allowed. Only allowed method: POST.'
            );
            return;
        }

        $email = $this->request->getParameter('email');
        $password = $this->request->getParameter('password');

        if (empty($email) || empty($password)) {
            $this->response->sendResponse(HttpStatusCodeEnum::CLIENT_ERROR_STATUS_CODE->value, 'Invalid credentials.');
            return;
        }

        try {
            $jw_token = $this->authentication_service->authenticateUser($email, $password);

            if (!$jw_token) {
                return;
            }

            $data = ['token' => $jw_token];
            $this->response->sendResponse(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value, 'Login successful.', $data);
        } catch (Exception $exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value, $exception->getMessage());
        }
    }

    public function logout(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
                'Method not allowed. Only allowed method: POST.'
            );
            return;
        }

        $token = $this->request->getHeader()->get('Authorization');

        if (empty($token)) {
            $this->response->sendResponse(HttpStatusCodeEnum::CLIENT_ERROR_STATUS_CODE->value, 'Token is required.');
            return;
        }

        try {
            $this->redisHandler->set($token, 'invalid', (int) $_ENV['AUTHENTICATION_EXPIRATION_TIME']);
            $this->response->sendResponse(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value, 'Logout successful.');
        } catch (Exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value, 'Logout failed.');
        }
    }
}
