<?php

ini_set('display_errors',1);
ini_set('display_starup_error',1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

$dotenv = Dotenv\Dotenv::create(__DIR__.'/..');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;
use Zend\Diactoros\Response\RedirectResponse;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('index', '/php/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'getIndex'
]);

$map->get('addJobs', '/php/job/add', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJob',
    'auth' => true
]);

$map->post('createJobs', '/php/job/add', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJob',
    'auth' => true
]);

$map->get('addProject', '/php/project/add', [
    'controller' => 'App\Controllers\ProjectsController',
    'action' => 'getAddProject',
    'auth' => true
]);

$map->get('addUser', '/php/user/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUser',
    'auth' => true
]);

$map->post('saveUser', '/php/user/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'saveUser',
    'auth' => true
]);

$map->get('login', '/php/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);

$map->get('logout', '/php/logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogout'
]);

$map->post('auth', '/php/auth', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);

$map->get('admin', '/php/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route) {
    echo 'No route';
}else{
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $needsAuth = $handlerData['auth'] ?? false;

    $sessionUserId = $_SESSION['userId'] ?? null;

    if($needsAuth && !$sessionUserId){
        $response = new RedirectResponse('/php/');
    }else{
        $controller = new $controllerName;
        $response = $controller->$actionName($request);
    }
    
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();
}