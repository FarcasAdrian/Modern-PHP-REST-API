<?php

require_once 'vendor/autoload.php';

use Classes\Router;

header("Content-Type: application/json");

$env = parse_ini_file('.env');
$_ENV = array_merge($_ENV, $env);

$router = new Router();
$router->route();
