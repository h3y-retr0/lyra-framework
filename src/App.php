<?php

namespace Lyra;

use Dotenv\Dotenv;
use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Drivers\PdoDriver;
use Lyra\Database\Model;
use Lyra\Http\HttpMethod;
use Lyra\Http\HttpNotFoundException;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Router;
use Lyra\Server\PhpNativeServer;
use Lyra\Server\Server;
use Lyra\Session\PhpNativeSessionStorage;
use Lyra\Session\Session;
use Lyra\Validation\Exceptions\ValidationException;
use Lyra\Validation\Rule;
use Lyra\View\LyraEngine;
use Lyra\View\View;
use Lyra\Config\Config;
use Throwable;

class App {
    /**
     * App's root directory.
     *
     * @var string
     */
    public static string $root;
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

    public Session $session;

    public DatabaseDriver $database;

    /**
     * Initialize the app.
     *
     * @return \Lyra\App
     */
    public static function bootstrap(string $root) {
        self::$root = $root;
        Dotenv::createImmutable($root)->load();
        Config::load("$root/config");
        $app = singleton(self::class);
        $app->router = new Router();
        $app->server = new PhpNativeServer();
        $app->request = $app->server->getRequest();
        $app->view = new LyraEngine(__DIR__ . "/../views/");
        $app->session = new Session(new PhpNativeSessionStorage());
        $app->database = singleton(DatabaseDriver::class, PdoDriver::class);
        $app->database->connect('mysql', 'localhost', 3306, 'lyra_framework', 'root', '');
        Model::setDatabaseDriver($app->database);
        Rule::loadDefaultRules();

        return $app;
    }

    public function prepareNextRequest() {
        if ($this->request->method() == HttpMethod::GET) {
            $this->session->set('_previous', $this->request->uri());
        }
    }

    public function terminate(Response $response) {
        $this->prepareNextRequest();
        $this->server->sendResponse($response);
        $this->database->close();
        exit();
    }

    public function run() {
        try {
            $this->terminate($this->router->resolve($this->request));
        } catch (HttpNotFoundException $e) {
            $this->abort(Response::text("Not fount")->setStatus(404));
        } catch (ValidationException $e) {
            $this->abort(back()->withErrors($e->errors(), 422));
        } catch (Throwable $e) {
            $response = json([
                "error" => $e::class,
                "message" => $e->getMessage(),
                "trace" => $e->getTrace()
            ]);

            $this->abort($response->setStatus(500));
        }
    }

    public function abort(Response $response) {
        $this->terminate($response);
    }
}
