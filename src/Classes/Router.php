<?php

declare(strict_types=1);

namespace Classes;

use Classes\Middleware\AuthMiddleware;
use Controllers\AuthenticationController;
use Controllers\UserController;

class Router
{
    private Request $request;
    private Response $response;
    private AuthMiddleware $authMiddleware;
    private UserController $userController;
    private AuthenticationController $authController;

    public function __construct(
        Request $request,
        Response $response,
        AuthMiddleware $authMiddleware,
        UserController $userController,
        AuthenticationController $authController
    )
    {
        $this->request = $request;
        $this->response = $response;
        $this->authMiddleware = $authMiddleware;
        $this->userController = $userController;
        $this->authController = $authController;
    }

    public function route(): void
    {
        $request_endpoint = $this->request->getRequestEndpoint();

        if (in_array($request_endpoint, ['user/login', 'user/logout'])) {
            if ($request_endpoint === 'user/login') {
                $this->authController->login();
            } else {
                $this->authController->logout();
            }
        } else if (in_array($request_endpoint, ['users', 'user', 'user/create', 'user/update', 'user/delete'])) {
            if ($request_endpoint !== 'user/create' && !$this->authMiddleware->handle($this->request)) {
                return;
            }

            switch ($request_endpoint) {
                case 'users':
                    $this->userController->getAll();
                    break;
                case 'user':
                    $this->userController->get();
                    break;
                case 'user/create':
                    $this->userController->create();
                    break;
                case 'user/update':
                    $this->userController->update();
                    break;
                case 'user/delete':
                    $this->userController->delete();
                    break;
                default:
                    $this->response->sendResponse(Response::NOT_FOUND_STATUS_CODE, 'Route not found.');
                    break;
            }
        } else {
            $this->response->sendResponse(Response::NOT_FOUND_STATUS_CODE, 'Route not found.');
        }
    }
}
