<?php

use Lyra\App;
use Lyra\Http\Middleware;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Route;
use Lyra\Validation\Rule;
use Lyra\Validation\Rules\Required;

require_once "../vendor/autoload.php";

$app = App::bootstrap();

$app->router->get('/test/{param}', function (Request $request) {
    return json($request->routeParameters());
});

$app->router->post('/test', function (Request $request) {
    return json($request->query());
});

$app->router->get('/redirect', function () {
    return redirect("/test");
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
            return json(["message" => "Not authenticated"])->setStatus(401);
        }

        $response =  $next($request);
        $response->setHeader('X-Test-Custom-Header', 'Hola');

        return $response;
    }
}

Route::get('/middlewares', fn (Request $request) => json(["message" => "ok"]))
    ->setMiddlewares([AuthMiddleware::class]);

Route::get('/html', fn (Request $request) => view('home', [
    'user' => ['name' => 'manolo', 'email' => 'manolo@gmail.com']
]));

Route::post('/validate', fn (Request $request) => json($request->validate([
    'test' => Rule::required(),
    'num' => Rule::number(),
    'email' => ['required_when:num,>,5', 'email']
], [
    'email' => [
        'email' => 'Custom message give me email'
    ]
])));

Route::get('/session', function (Request $request) {
    // session()->flash('test', 'test');
    return json($_SESSION);
});

$app->run();
