<?php

namespace Lyra\Http;

class Controller {
    /**
     * HTTP middlewares.
     *
     * @var array
     */
    protected array $middlewares = [];

    public function middlewares(): array {
        return $this->middlewares;
    }

    public function setMiddlewares(array $middlewares): self {
        $this->middlewares = array_map(fn ($middleware) => new $middleware(), $middlewares);
        return $this;
    }
}
