<?php

namespace Lyra\Auth;

use Lyra\Auth\Authenticators\Authenticator;
use Lyra\Database\Model;

class Authenticatable extends Model {
    public function id(): int|string {
        return $this->{$this->primaryKey};
    }

    public function login() {
        app(Authenticator::class)->login($this);
    }

    public function logout() {
        app(Authenticator::class)->logout($this);
    }

    public function isAuthenticated() {
        app(Authenticator::class)->isAuthenticated($this);
    }
}
