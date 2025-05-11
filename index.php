<?php

require "./Router.php";

$router = new Router();

$router->get('/test', fn () => "GET OK");
$router->post('/test', fn () => "POST OK");
$router->put('/test', fn () => "PUT OK");
$router->patch('/test', fn () => "PATCH OK");
$router->delete('/test', fn () => "DELETE OK");


try {
    $action = $router->resolve();
    print($action());
} catch (HttpNotFoundException $e) {
    print("Not found");
    http_response_code(404);
}
