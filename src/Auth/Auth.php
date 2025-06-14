<?php

namespace Lyra\Auth;

use Lyra\Auth\Authenticators\Authenticator;

class Auth {
    public static function user(): ?Authenticatable {
        return app(Authenticator::class)->resolve();
    }

    public static function isGuest(): bool {
        return is_null(self::user());
    }
}
