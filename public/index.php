<?php

use Lyra\App;
use Lyra\Http\Middleware;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Route;

require_once "../vendor/autoload.php";

$app = App::bootstrap();

$app->router->get('/test/{param}', function (Request $request) {
    return Response::json($request->routeParameters());
});

$app->router->post('/test', function (Request $request) {
    return Response::json($request->query());
});

$app->router->get('/redirect', function () {
    return Response::redirect("/test");
});

$app->router->put('/test', function () {
    return "PUT OK";
});

$app->router->patch ('/test', function () {
    return "PATCH OK";
});

$app->router->delete('/test', function () {
    return "DELETE OK";
});

class AuthMiddleware implements Middleware {
    public function handle(Request $request, Closure $next): Response {
        if ($request->headers('Authorization') != 'test') {
            return Response::json(["message" => "Not authenticated"])->setStatus(401);
        }

        $response =  $next($request);
        $response->setHeader('X-Test-Custom-Header', 'Hola');

        return $response;
    }
}

Route::get('/middlewares', fn (Request $request) => Response::json(["message" => "ok"]))
    ->setMiddlewares([AuthMiddleware::class]);

$app->run();
