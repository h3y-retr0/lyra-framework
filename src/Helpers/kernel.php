<?php

use Lyra\App;
use Lyra\Container\Container;

function app(string $class = App::class) {
    return Container::resolve($class);
}

function singleton(string $class, string|callable|null $build = null) {
    return Container::singleton($class, $build);
}
