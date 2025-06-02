<?php

namespace Lyra;

use Lyra\Container\Container;
use Lyra\Http\HttpNotFoundException;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Router;
use Lyra\Server\PhpNativeServer;
use Lyra\Server\Server;
use Lyra\View\LyraEngine;
use Lyra\View\View;

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
        $app = Container::singleton(self::class);
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
            $response = Response::text("Not found")->setStatus(404);
            $this->server->sendResponse($response);
        }
    }
}
