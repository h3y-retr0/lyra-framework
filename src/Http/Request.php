<?php

namespace Lyra\Http;

use Lyra\Server\Server;

/**
 * HTTP Request sent by the client.
 */
class Request {
    /**
     * URI requested by the client.
     *
     * @var string
     */
    protected string $uri;

    /**
     * HTTP Method used for this request.
     *
     * @var HttpMethod
     */
    protected HttpMethod $method;

    /**
     * Request data sent with POST or PUT methods.
     *
     * @var array
     */
    protected array $data;

    /**
     * Query params.
     *
     * @var array
     */

    protected ?array $query;

    /**
     * Create a new request instance from the given `$server`.
     *
     * @param Server $server
     */
    public function __construct(Server $server) {
        $this->uri = $server->requestUri();
        $this->method = $server->requestMethod();
        $this->data = $server->postData();
        $this->query = $server->queryParams();
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function uri(): string {
        return $this->uri;
    }

    /**
     * Get the requset HTTP method.
     *
     * @return HttpMethod
     */
    public function method(): HttpMethod {
        return $this->method;
    }
}
