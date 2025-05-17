<?php

namespace Lyra\Tests;

use Lyra\HttpMethod;
use Lyra\Server;

class MockServer implements Server{
    public function __construct(
        public string $uri, 
        public HttpMethod $method
        ) {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function requestUri(): string {
        return $this->uri;
    }

    public function requestMethod(): HttpMethod {
        return $this->method;
    }

    public function postData(): array {
        return [];
    }

    public function queryParams(): array {
        return[];
    }
}
