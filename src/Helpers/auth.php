<?php

use Lyra\Auth\Auth;
use Lyra\Auth\Authenticatable;

function auth(): ?Authenticatable {
    return Auth::user();
}

function isGuest(): bool {
    return Auth::isGuest();
}
