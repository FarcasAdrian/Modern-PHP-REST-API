<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Classes\App;
use Services\ServiceContainer;
use Classes\Db\DatabaseMySQLType;
use Classes\Config;
use Factories\ControllerFactory;
use Classes\Request;
use Classes\Router;

header("Content-Type: application/json");

$env = parse_ini_file('.env');
$_ENV = array_merge($_ENV, $env);

$container = new ServiceContainer();
App::setContainer($container);

App::bind('Interfaces\DatabaseTypeInterface', fn() => DatabaseMySQLType::getInstance());
App::bind('Interfaces\ConfigInterface', fn() => Config::getInstance());
App::bind('Interfaces\DatabaseInterface', fn() => App::resolve('Classes\Db\MySQLDatabase'));
App::bind('Interfaces\AuthMiddlewareInterface', fn() => App::resolve('Middlewares\JwtTokenAuthMiddleware'));
App::bind('Interfaces\RepositoryInterface', fn() => App::resolve('Classes\User\UserRepository'));
App::bind('Interfaces\EntityInterface', fn() => App::resolve('Classes\User\UserEntity'));
App::bind('Interfaces\EntityTransformerInterface', fn() => App::resolve('Transformers\UserEntityTransformer'));
App::bind('Interfaces\ServerParameterInterface', fn() => App::resolve('Classes\ServerParameter'));
App::bind('Interfaces\QueryParameterInterface', fn() => App::resolve('Classes\QueryParameter'));
App::bind('Interfaces\HeaderInterface', fn() => App::resolve('Classes\Header'));
App::bind('Interfaces\CookieInterface', fn() => App::resolve('Classes\Cookie'));
App::bind('Interfaces\AuthenticationServiceInterface', fn() => App::resolve('Services\AuthenticationService'));
App::bind('Interfaces\ResponseInterface', fn() => App::resolve('Classes\Response'));
App::bind('Interfaces\EntityDTOServiceInterface', fn() => App::resolve('Services\UserDTOService'));
App::bind('Interfaces\UserServiceInterface', fn() => App::resolve('Services\UserService'));

$controllerFactory = App::resolve(ControllerFactory::class);
$equest = App::resolve(Request::class);
$router = new Router($equest->getRequestUri());
$controllerFactory->makeFromRouter($router);
