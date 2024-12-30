<?php

namespace Classes;

use Classes\Db\Database;
use Classes\Middleware\AuthMiddleware;
use Classes\User\User;
use Classes\User\UserEntity;
use Controllers\AuthenticationController;
use Controllers\UserController;
use Services\AuthenticationService;
use Services\UserService;
use Services\ValidationService;

class Router
{
    private Request $request;
    private Response $response;
    private Database $database;
    private User $user;
    private UserService $user_service;
    private RedisHandler $redis_handler;
    private AuthenticationService $authentication_service;
    private AuthMiddleware $auth_middleware;
    private ValidationService $validation_service;
    private UserEntity $user_entity;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->database = new Database();
        $this->user = new User($this->database);
        $this->user_service = new UserService();
        $this->redis_handler = new RedisHandler();
        $this->authentication_service = new AuthenticationService($this->user, $this->response, $this->user_service);
        $this->auth_middleware = new AuthMiddleware($this->authentication_service, $this->response);
        $this->validation_service = new ValidationService();
        $this->user_entity = new UserEntity($this->validation_service, $this->user_service);
    }

    public function route(): void
    {
        $request_endpoint = $this->request->getRequestEndpoint();

        if (in_array($request_endpoint, ['user/login', 'user/logout'])) {
            $authentication_controller = new AuthenticationController(
                $this->authentication_service,
                $this->response,
                $this->request,
                $this->redis_handler
            );

            if ($request_endpoint === 'user/login') {
                $authentication_controller->login();
            } else {
                $authentication_controller->logout();
            }

        } else if (in_array($request_endpoint, ['users', 'user', 'user/create', 'user/update', 'user/delete'])) {
            if ($request_endpoint !== 'user/create' && !$this->auth_middleware->handle($this->request)) {
                return;
            }

            $validation_service = new ValidationService();
            $user_controller = new UserController(
                $this->user,
                $this->response,
                $this->request,
                $validation_service,
                $this->user_entity
            );

            switch ($request_endpoint) {
                case 'users':
                    $user_controller->getAll();
                    break;
                case 'user':
                    $user_controller->get();
                    break;
                case 'user/create':
                    $user_controller->create();
                    break;
                case 'user/update':
                    $user_controller->update();
                    break;
                case 'user/delete':
                    $user_controller->delete();
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
