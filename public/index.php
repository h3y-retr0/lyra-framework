<?php

use Lyra\Http\HttpNotFoundException;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Router;
use Lyra\Server\PhpNativeServer;

require_once "../vendor/autoload.php";

$router = new Router();

$router->get('/test/{param}', function (Request $request) {
    return Response::json($request->routeParameters());
});

$router->post('/test', function (Request $request) {
    return Response::json($request->query());
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
    $request = $server->getRequest();
    $route = $router->resolve($request);
    $request->setRoute($route);
    $action = $route->action();
    $response = $action($request);
    $server->sendResponse($response);
} catch (HttpNotFoundException $e) {
    $response = Response::text("Not found")->setStatus(404);
    $server->sendResponse($response);
}
