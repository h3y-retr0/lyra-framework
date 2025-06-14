<?php

namespace Lyra\Providers;

use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Drivers\PdoDriver;

class DatabaseDriverServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("database.connection", "mysql")) {
            // Since PDO works also with PostgreSQL
            "mysql", "pgsql" => singleton(DatabaseDriver::class, PdoDriver::class),
        };
    }
}
