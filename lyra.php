<?php

require_once "./vendor/autoload.php";

use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Drivers\PdoDriver;
use Lyra\Database\Migrations\Migrator;

$driver =  singleton(DatabaseDriver::class, PdoDriver::class);
$driver->connect('mysql', 'localhost', 3306, 'lyra_framework', 'root', '');

$migrator = new Migrator(
    __DIR__ . "/database/migrations",
    __DIR__ . "/templates",
    $driver
);

if ($argv[1] == "make:migration") {
    $migrator->make($argv[2]);
} else if ($argv[1] == "migrate") {
    $migrator->migrate();
}