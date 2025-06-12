<?php

use Lyra\App;
use Lyra\Config\Config;
use Lyra\Container\Container;

function app(string $class = App::class) {
    return Container::resolve($class);
}

function singleton(string $class, string|callable|null $build = null) {
    return Container::singleton($class, $build);
}

function env(string $var, $default = null) {
    return $_ENV[$var] ?? $default;
}

function config(string $configuration, $default = null) {
    return Config::get($configuration, $default);
}

function resourcesDirectory(): string {
    return App::$root . "/resources";
}
