<?php

namespace Lyra\Providers;

use Lyra\Server\PhpNativeServer;
use Lyra\Server\Server;

class ServerServiceProvider implements ServiceProvider {
    public function registerServices() {
        singleton(Server::class, PhpNativeServer::class);
    }
}
