<?php

namespace Lyra\Providers;

use Lyra\View\LyraEngine;
use Lyra\View\View;

class ViewServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("view.engine", "lyra")) {
            "lyra" => singleton(View::class, fn () => new LyraEngine(config("view.path"))),
        };
    }
}
