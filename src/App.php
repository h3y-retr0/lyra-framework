<?php

namespace Lyra;

use Dotenv\Dotenv;
use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Model;
use Lyra\Http\HttpMethod;
use Lyra\Http\HttpNotFoundException;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\Routing\Router;
use Lyra\Server\Server;
use Lyra\Session\Session;
use Lyra\Validation\Exceptions\ValidationException;
use Lyra\View\View;
use Lyra\Config\Config;
use Lyra\Session\SessionStorage;
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

        $app = singleton(self::class);

        return $app
            ->loadConfig()
            ->runServiceProviders('boot')
            ->setHttpHandlers()
            ->setUpDatabaseConnection()
            ->runServiceProviders('runtime');
    }

    protected function loadConfig(): self {
        Dotenv::createImmutable(self::$root)->load();
        Config::load(self::$root . "/config");

        return $this;
    }

    protected function runServiceProviders(string $type): self {
        foreach (config("providers.$type", []) as $provider) {
            $provider = new $provider();
            $provider->registerServices();
        }

        return $this;
    }

    protected function setHttpHandlers(): self {
        $this->router = singleton(Router::class);
        $this->server = app(Server::class);
        $this->request = singleton(Request::class, fn () => $this->server->getRequest());
        $this->session = singleton(Session::class, fn () => new Session(app(SessionStorage::class)));

        return $this;
    }

    protected function setUpDatabaseConnection(): self {
        $this->database = app(DatabaseDriver::class);

        $this->database->connect(
            config("database.connection"),
            config("database.host"),
            config("database.port"),
            config("database.database"),
            config("database.username"),
            config("database.password"),
        );

        Model::setDatabaseDriver($this->database);

        return $this;
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
            $this->abort(Response::text("Not found")->setStatus(404));
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
