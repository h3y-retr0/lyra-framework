<?php

namespace Lyra\Providers;

use Lyra\Session\PhpNativeSessionStorage;
use Lyra\Session\SessionStorage;

class SessionStorageServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("session.storage", "native")) {
            "native" => singleton(SessionStorage::class, PhpNativeSessionStorage::class),
        };
    }
}
