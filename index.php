<?php

require_once 'vendor/autoload.php';

use Controllers\UserController;
use Classes\Db\Database;
use Classes\User;
use Classes\Response;
use Classes\Request;

header("Content-Type: application/json");

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/api/', $_SERVER['REQUEST_URI']);
$endpoint = explode('?', end($request_uri));
$request_endpoint = is_array($endpoint) ? $endpoint[0] : '';
$endpoint_parameters = is_array($endpoint) && isset($endpoint[1]) ? $endpoint[1] : '';

if (in_array($request_endpoint, ['users', 'user'])) {
    $database = new Database();
    $user = new User($database);
    $response = new Response();
    $request = new Request();
    $user_controller = new UserController($user, $response, $request);

    if ($request_endpoint == 'users') {
        $user_controller->getAll();
    } else {
        $user_controller->get();
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Route not found."]);
}
