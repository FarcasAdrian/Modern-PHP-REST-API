<?php

require_once 'vendor/autoload.php';

use Controllers\UserController;
use Classes\Db\Database;
use Classes\User;
use Classes\Response;

header("Content-Type: application/json");

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/api/', $_SERVER['REQUEST_URI']);
$endpoint = explode('?', end($request_uri));
$request_endpoint = is_array($endpoint) ? $endpoint[0] : '';
$endpoint_parameters = is_array($endpoint) && isset($endpoint[1]) ? $endpoint[1] : '';

if ($request_endpoint == 'users') {
    $database = new Database();
    $user = new User($database);
    $response = new Response();
    $user_controller = new UserController($user, $response);
    $user_controller->getAll();
} else if ($request_endpoint == 'user') {
    $database = new Database();
    $user = new User($database);
    $response = new Response();
    $user_controller = new UserController($user, $response);
    $user_controller->get();
} else {
    http_response_code(400);
    echo json_encode(["message" => "Route not found."]);
}

switch ($request_endpoint) {
    case 'users':
        $database = new Database();
        $user = new User($database);
        $response = new Response();
        $user_controller = new UserController($user, $response);
        $user_controller->getAll();

        if ($request_method == 'GET') {
            $database = new Database();
            $user = new User($database);
            $response = new Response();
            $user_controller = new UserController($user, $response);
            $user_controller->getAll();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Method Now Allowed."]);
        }
        break;
    case 'user':

    default:
        http_response_code(400);
        echo json_encode(["message" => "Route not found."]);
        break;
}
