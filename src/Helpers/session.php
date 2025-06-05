<?php

use Lyra\Session\Session;

function session(): Session {
    return app()->session;
}