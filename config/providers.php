<?php

return [
    'boot' => [
        \Lyra\Providers\ServerServiceProvider::class,
        \Lyra\Providers\DatabaseDriverServiceProvider::class,
        \Lyra\Providers\DatabaseDriverServiceProvider::class,
        \Lyra\Providers\ViewServiceProvider::class,
    ],
    'runtime' => [

    ],
];