<?php

namespace Lyra\Server;

use Lyra\Http\HttpMethod;
use Lyra\Http\Request;
use Lyra\Http\Response;
use Lyra\storage\File;

/**
 * PHP native server that uses `$_SERVER` global.
 */
class PhpNativeServer implements Server {
    /**
     * @inheritDoc
     */
    public function getRequest(): Request {
        return (new Request())
            ->setUri(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH))
            ->setMethod(HttpMethod::from($_SERVER["REQUEST_METHOD"]))
            ->setHeaders(getallheaders())
            ->setPostData($this->requestData())
            ->setQueryParameters($_GET)
            ->setFiles($this->uploadedFiles());
    }

    /**
     * @inheritDoc
     */
    public function sendResponse(Response $response) {
        // PHP sends Content-Type header by default, but it has to be removed if
        // the response has no content. Content-Type header can't be removed
        // unless it is set to some value before.
        header("Content-Type: None");
        header_remove("Content-Type");

        $response->prepare();
        http_response_code($response->status());
        foreach ($response->headers() as $header => $value) {
            header("$header: $value");
        }
        print($response->content());
    }

    protected function uploadedFiles(): array {
        $files = [];
        foreach ($_FILES as $key => $file) {
            if (!empty($file["tmp_name"])) {
                $files[$key] = new File(
                    file_get_contents($file["tmp_name"]),
                    $file["type"],
                    $file["name"],
                );
            }
        }

        return $files;
    }

    protected function requestData(): array {
        $headers = getallheaders();

        $isJson = isset($headers["Content-Type"])
            && $headers["Content-Type"] === "application/json";

        if ($_SERVER["REQUEST_METHOD"] == HttpMethod::POST && !$isJson) {
            return $_POST;
        }

        if ($isJson) {
            $data = json_decode(file_get_contents("php://input"), associative: true);
        } else {
            parse_str(file_get_contents("php://input"), $data);
        }

        return $data;
    }
}
