<?php

namespace Lyra;

use Lyra\Http\HttpNotFoundException;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Router;
use Lyra\Server\PhpNativeServer;
use Lyra\Server\Server;
use Lyra\Validation\Exceptions\ValidationException;
use Lyra\View\LyraEngine;
use Lyra\View\View;
use Throwable;

class App {
    /**
     * App router.
     *
     * @var Router
     */
    public Router $router;

    /**
     * App request.
     *
     * @var Request
     */
    public Request $request;

    /**
     * App server.
     *
     * @var Server
     */
    public Server $server;

    public View $view;

    /**
     * Initialize the app.
     *
     * @return \Lyra\App
     */
    public static function bootstrap() {
        $app = singleton(self::class);
        $app->router = new Router();
        $app->server = new PhpNativeServer();
        $app->request = $app->server->getRequest();
        $app->view = new LyraEngine(__DIR__ . "/../views/");

        return $app;
    }

    public function run() {
        try {
            $response = $this->router->resolve($this->request);
            $this->server->sendResponse($response);
        } catch (HttpNotFoundException $e) {
            $this->abort(Response::text("Not fount")->setStatus(404));
        } catch (ValidationException $e) {
            $this->abort(json($e->errors())->setStatus(422));
        } catch (Throwable $e) {
            $response = json([
                "message" => $e->getMessage(),
                "trace" => $e->getTrace()
            ]);

            $this->abort($response);
        }
    }

    public function abort(Response $response) {
        $this->server->sendResponse($response);
    }
}
