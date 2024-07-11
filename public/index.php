<?php
require '../vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', 'UserController@index');
    $r->addRoute('POST', '/users', 'UserController@store');
    $r->addRoute('GET', '/users/{id:\d+}', 'UserController@show');
    $r->addRoute('PUT', '/users/{id:\d+}', 'UserController@update');
    $r->addRoute('DELETE', '/users/{id:\d+}', 'UserController@destroy');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // Handle 404
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // Handle 405
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($controller, $method) = explode("@", $handler);
        $controller = "App\\Controllers\\$controller";
        (new $controller)->$method($vars);
        break;
}
