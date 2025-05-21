<?php

use Lyra\Http\HttpNotFoundException;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Router;
use Lyra\Server\PhpNativeServer;

require_once "../vendor/autoload.php";

$router = new Router();

$router->get('/test', function (Request $request) {
    /* Builder design pattern */
    return Response::text("GET OK");
});

$router->post('/test', function (Request $request) {
    return Response::text("POST OK");
});

$router->get('/redirect', function () {
    return Response::redirect("/test");
});

$router->put('/test', function () {
    return "PUT OK";
});

$router->patch ('/test', function () {
    return "PATCH OK";
});

$router->delete('/test', function () {
    return "DELETE OK";
});


$server = new PhpNativeServer();
try {
    $request = new Request($server);
    $route = $router->resolve($request);
    $action = $route->action();
    $response = $action($request);
    $server->sendResponse($response);
} catch (HttpNotFoundException $e) {
    $response = Response::text("Not found")->setStatus(404);
    $server->sendResponse($response);
}
