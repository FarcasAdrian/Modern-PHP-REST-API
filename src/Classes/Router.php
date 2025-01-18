<?php

declare(strict_types=1);

namespace Classes;

use Classes\Middleware\AuthMiddleware;
use Controllers\AuthenticationController;
use Controllers\UserController;
use Enums\HttpStatusCodeEnum;

class Router
{
    public function __construct(
        private Request $request,
        private Response $response,
        private AuthMiddleware $authMiddleware,
        private UserController $userController,
        private AuthenticationController $authController
    ) {}

    public function route(): void
    {
        $request_endpoint = $this->request->getRequestEndpoint();

        if (in_array($request_endpoint, ['user/login', 'user/logout'])) {
            $request_endpoint === 'user/login' ? $this->authController->login() : $this->authController->logout();
        } else if (in_array($request_endpoint, ['users', 'user', 'user/create', 'user/update', 'user/delete'])) {
            if ($request_endpoint !== 'user/create' && !$this->authMiddleware->handle($this->request)) {
                return;
            }

            match($request_endpoint) {
                'users' => $this->userController->getAll(),
                'user' => $this->userController->get(),
                'user/create' => $this->userController->create(),
                'user/update' => $this->userController->update(),
                'user/delete' => $this->userController->delete(),
                default => $this->response->sendResponse(HttpStatusCodeEnum::NOT_FOUND_STATUS_CODE->value, 'Route not found.'),
            };
        } else {
            $this->response->sendResponse(HttpStatusCodeEnum::NOT_FOUND_STATUS_CODE->value, 'Route not found.');
        }
    }
}
