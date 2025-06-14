<?php

namespace App\Providers;

use Lyra\App;
use Lyra\Providers\ServiceProvider;
use Lyra\Routing\Route;

class RouteServiceProvider implements ServiceProvider {
    public function registerServices() {
        Route::load(App::$root . "/routes");
    }
}
