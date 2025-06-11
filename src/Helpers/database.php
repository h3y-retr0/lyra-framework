<?php

use Lyra\Database\Drivers\DatabaseDriver;

function db() {
    return app(DatabaseDriver::class);
}
