<?php

namespace Lyra\Cli;

use Dotenv\Dotenv;
use Lyra\App;
use Lyra\Cli\Commands\MakeMigration;
use Lyra\Cli\Commands\Migrate;
use Lyra\Cli\Commands\MigrateRollback;
use Lyra\Config\Config;
use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Migrations\Migrator;
use Symfony\Component\Console\Application;

class Cli {
    public static function bootstrap(string $root): self {
        App::$root = $root;
        Dotenv::createImmutable($root)->load();
        Config::load($root . "/config");

        foreach (config("providers.cli") as $provider) {
            (new $provider())->registerServices();
        }

        app(DatabaseDriver::class)->connect(
            config("database.connection"),
            config("database.host"),
            config("database.port"),
            config("database.database"),
            config("database.username"),
            config("database.password"),
        );

        singleton(
            Migrator::class,
            fn () => new Migrator(
                "$root/database/migrations",
                resourcesDirectory() . "/templates",
                app(DatabaseDriver::class)
            )
        );

        return new self();
    }

    public function run() {
        $cli = new Application("Lyra");

        $cli->addCommands([
            new MakeMigration(),
            new Migrate(),
            new MigrateRollback(),
        ]);

        $cli->run();
    }
}
