<?php

namespace Lyra\Http;

use Lyra\Routing\Route;
use Lyra\storage\File;
use Lyra\Validation\Validator;

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
     * ROute matched by URI.
     *
     * @var Route
     */
    protected Route $route;

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
     * Query parameters.
     *
     * @var array
     */
    protected ?array $query;

    protected array $headers = [];

    /**
     * Uploaded files.
     *
     * @var array
     */
    protected array $files = [];

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function uri(): string {
        return $this->uri;
    }

    /**
     * Set request URI
     *
     * @param string $uri
     * @return self
     */
    public function setUri(string $uri): self {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Get route matched by the URI of this request.
     *
     * @return Route
     */
    public function route(): Route {
        return $this->route;
    }
    /**
     * Set route for this request.
     *
     * @param Route $route
     * @return self
     */
    public function setRoute(Route $route): self {
        $this->route = $route;
        return $this;
    }
    /**
     * Get the requset HTTP method.
     *
     * @return HttpMethod
     */
    public function method(): HttpMethod {
        return $this->method;
    }

    /**
     * Set request HTTP method.
     *
     * @param HttpMethod $method
     * @return self
     */
    public function setMethod(HttpMethod $method): self {
        $this->method = $method;
        return $this;
    }

    public function headers(?string $key = null): array|string|null {
        if (is_null($key)) {
            return $this->headers;
        }

        return $this->headers[strtolower($key)] ?? null;
    }

    public function setHeaders(array $headers): self {
        foreach ($headers as $header => $value) {
            $this->headers[strtolower($header)] = $value;
        }

        return $this;
    }


    /**
     * Get POST as key-value or get only specific key.
     *
     * @return array
     */
    public function data(?string $key = null): array|string|null {
        if (is_null($key)) {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Set POST data.
     *
     * @param array $data
     * @return self
     */
    public function setPostData(array $data): self {
        $this->data = $data;
        return $this;
    }

    /**
     * Get all query parameters as key-value or get only specific key.
     *
     * @return array
     */
    public function query(?string $key = null): array|string|null {
        if (is_null($key)) {
            return $this->query;
        }
        return $this->query[$key] ?? null;
    }

    /**
     * Set query parameters.
     *
     * @param array $query
     * @return self
     */
    public function setQueryParameters(array $query): self {
        $this->query = $query;
        return $this;
    }

    /**
     * Get all route parameters as key-value or get only specific key.
     *
     * @return array
     */
    public function routeParameters(?string $key = null): array|string|null {
        $params = $this->route->parseParameters($this->uri);

        if (is_null($key)) {
            return $params;
        }

        return $params[$key] ?? null;
    }

    /**
     * Validate the data from this `Request` using `Validator` class.
     *
     * @param array $rules
     * @param array|null $messages
     * @return array
     */
    public function validate(array $rules, ?array $messages = []): array {
        $validator = new Validator($this->data);

        return $validator->validate($rules, $messages);
    }
    
    /**
     * Get file from request.
     *
     * @param string $name
     * @return File|null
     */
    public function file(string $name): ?File {
        return $this->files[$name] ?? null;
    }

    /**
     * Set uploaded files.
     *
     * @param array $files
     * @return self
     */
    public function setFiles(array $files): self {
        $this->files = $files;
        return $this;
    }
}
