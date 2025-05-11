<?php

require_once "../vendor/autoload.php";

use Lyra\HttpNotFoundException;
use Lyra\Route;
use Lyra\Router;

$router = new Router();

$router->get('/test', fn () => "GET OK");
$router->post('/test', fn () => "POST OK");
$router->put('/test', fn () => "PUT OK");
$router->patch('/test', fn () => "PATCH OK");
$router->delete('/test', fn () => "DELETE OK");


try {
    $route = $router->resolve($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);
    $action = $route->action();
    print($action());
} catch (HttpNotFoundException $e) {
    print("Not found");
    http_response_code(404);
}
