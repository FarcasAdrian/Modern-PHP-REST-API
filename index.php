<?php

require_once 'vendor/autoload.php';

use Classes\Db\Database;
use Classes\Request;
use Classes\Response;
use Classes\User\User;
use Controllers\UserController;
use Services\ValidationService;

header("Content-Type: application/json");

$response = new Response();
$request = new Request();
$request_endpoint = $request->getRequestEndpoint();

if (in_array($request_endpoint, ['users', 'user', 'user/create', 'user/update', 'user/delete'])) {
    $database = new Database();
    $user = new User($database);
    $request = new Request();
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
