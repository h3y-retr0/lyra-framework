<?php

return [
    /**
     * Service providers that will run before booting application.
     */
    'boot' => [
        /**
         * Lyra framework service provicers
         */
        Lyra\Providers\ServerServiceProvider::class,
        Lyra\Providers\DatabaseDriverServiceProvider::class,
        Lyra\Providers\SessionStorageServiceProvider::class,
        Lyra\Providers\ViewServiceProvider::class,
        Lyra\Providers\AuthenticatorServiceProvider::class,
        Lyra\Providers\HasherServiceProvider::class,

        /**
         * Package service providers
         */
    ],
    /**
     * Service providers that will run after booting application.
     */
    'runtime' => [
        App\Providers\RuleServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],

    'cli' => [
        Lyra\Providers\DatabaseDriverServiceProvider::class,
    ],
];