<?php

require_once 'vendor/autoload.php';

use Classes\Db\Database;
use Classes\Request;
use Classes\Response;
use Classes\User\User;
use Controllers\UserController;
use Controllers\AuthenticationController;
use Services\ValidationService;
use Classes\Middleware\AuthMiddleware;
use Services\AuthenticationService;
use Services\UserService;

header("Content-Type: application/json");

$env = parse_ini_file('.env');
$_ENV = array_merge($_ENV, $env);

$response = new Response();
$request = new Request();
$request_endpoint = $request->getRequestEndpoint();

$database = new Database();
$user = new User($database);
$user_service = new UserService();
$authentication_service = new AuthenticationService($user, $response, $user_service);
$middleware = new AuthMiddleware($authentication_service, $response);

if (in_array($request_endpoint, ['user/login', 'user/logout'])) {
    $authentication_controller = new AuthenticationController($authentication_service, $response, $request);

    if ($request_endpoint === 'user/login') {
        $authentication_controller->login();
    } else {
        $authentication_controller->logout();
    }

} else if (in_array($request_endpoint, ['users', 'user', 'user/create', 'user/update', 'user/delete'])) {
    if ($request_endpoint !== 'user/create' && !$middleware->handle($request)) {
        return;
    }

    $validation_service = new ValidationService();
    $user_controller = new UserController($user, $response, $request, $validation_service);

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
            $response->sendResponse(Response::NOT_FOUND_STATUS_CODE, 'Route not found.');
            break;
    }
} else {
    $response->sendResponse(Response::NOT_FOUND_STATUS_CODE, 'Route not found.');
}
