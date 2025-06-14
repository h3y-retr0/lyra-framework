<?php

namespace Lyra\Providers;

use Lyra\Crypto\Bcrypt;
use Lyra\Crypto\Hasher;

class HasherServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("hasing.hasher", "bcrypt")) {
            "bcrypt" => singleton(Hasher::class, Bcrypt::class),
        };
    }
}
