<?php

namespace Lyra\Providers;

use Lyra\Auth\Authenticators\Authenticator;
use Lyra\Auth\Authenticators\SessionAuthenticator;

class AuthenticatorServiceProvider implements ServiceProvider {
    public function registerServices() {
        match (config("auth.method", "session")) {
            "session" => singleton(Authenticator::class, SessionAuthenticator::class),
        };
    }
}
