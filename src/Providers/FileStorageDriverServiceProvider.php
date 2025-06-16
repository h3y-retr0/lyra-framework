<?php

namespace Lyra\Providers;

use Lyra\App;
use Lyra\storage\Drivers\DiskFileStorage;
use Lyra\storage\Drivers\FileStorageDriver;

class FileStorageDriverServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("storage.driver", "disk")) {
            "disk" => singleton(
                FileStorageDriver::class,
                fn () => new DiskFileStorage(
                    App::$root . "/storage",
                    "storage",
                    config("app.url")
                )
            ),
        };
    }
}
