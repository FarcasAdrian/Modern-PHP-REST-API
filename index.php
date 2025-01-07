<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Services\ServiceContainer;
use Classes\Router;

header("Content-Type: application/json");

$env = parse_ini_file('.env');
$_ENV = array_merge($_ENV, $env);

$container = new ServiceContainer();
$router = $container->get(Router::class);
$router->route();
