<?php

declare(strict_types=1);

namespace Factories;

use Classes\App;
use Classes\Request;
use Controllers\AuthenticationController;
use Controllers\UserController;
use Classes\Router;
use Enums\HttpStatusCodeEnum;
use Interfaces\AuthMiddlewareInterface;
use Interfaces\ResponseInterface;
use stdClass;

class ControllerFactory
{
    public function __construct(private ResponseInterface $response, private AuthMiddlewareInterface $authMiddleware, private Request $request) {}

    public function make(string $route): mixed
    {
        return match ($route) {
            'users', 'user', 'user/create', 'user/update', 'user/delete' => App::resolve(UserController::class),
            'user/login', 'user/logout' => App::resolve(AuthenticationController::class),
            default => new stdClass(),
        };
    }

    public function makeFromRouter(Router $router): void
    {
        $route = $router->parseRoute();
        $controller = $this->make($router->parseRoute());

        if (in_array($route, ['user/login', 'user/logout'])) {
            $route === 'user/login' ? $controller->login() : $controller->logout();
        } else if (in_array($route, ['users', 'user', 'user/create', 'user/update', 'user/delete'])) {
            if ($route !== 'user/create' && !$this->authMiddleware->handle($this->request)) {
                return;
            }

            match ($route) {
                'users' => $controller->getAll(),
                'user' => $controller->get(),
                'user/create' => $controller->create(),
                'user/update' => $controller->update(),
                'user/delete' => $controller->delete(),
                default => $this->response->sendResponse(HttpStatusCodeEnum::NOT_FOUND_STATUS_CODE->value, 'Route not found.'),
            };
        } else {
            $this->response->sendResponse(HttpStatusCodeEnum::NOT_FOUND_STATUS_CODE->value, 'Route not found.');
        }
    }
}
