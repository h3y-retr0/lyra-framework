<?php

namespace Lyra\Tests\Database;

use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Drivers\PdoDriver;
use Lyra\Database\Model;
use PDOException;

trait RefreshDatabase {
    protected function setUp(): void {
        if (is_null($this->driver)) {
            $this->driver =  singleton(DatabaseDriver::class, PdoDriver::class);
            Model::setDatabaseDriver($this->driver);
            try {
                $this->driver->connect('mysql', 'localhost', 3306, 'lyra_framework_tests', 'root', '');
            } catch (PDOException $e) {
                $this->markTestSkipped("Can't connect to test database: {$e->getMessage()}");
            }
        }
    }

    protected function tearDown(): void {
        $this->driver->statement("DROP DATABASE IF EXISTS lyra_framework_tests");
        $this->driver->statement("CREATE DATABASE lyra_framework_tests");
    }
}
