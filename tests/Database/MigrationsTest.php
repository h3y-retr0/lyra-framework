<?php

namespace Lyra\Tests\Database;

use Lyra\Database\Drivers\DatabaseDriver;
use Lyra\Database\Migrations\Migrator;
use PDOException;
use PHPUnit\Framework\TestCase;

class MigrationsTest extends TestCase {
    use RefreshDatabase {
        setUp as protected dbSetUp;
        tearDown as protected dbTearDown;
    }

    protected ?DatabaseDriver $driver = null;
    protected $templatesDirectory = __DIR__ . "/templates";
    protected $migrationsDirectory = __DIR__ . "/migrations";
    protected $expecteMigrationsDirectory = __DIR__ . "/expected";
    protected Migrator $migrator;


    protected function setUp(): void {
        if (!file_exists($this->migrationsDirectory)) {
            mkdir($this->migrationsDirectory);
        }
        $this->dbSetUp();

        $this->migrator = new Migrator(
            $this->migrationsDirectory,
            $this->templatesDirectory,
            $this->driver,
            false,
        );
    }
    private function deleteDirectory(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = "$dir/$item";
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    protected function tearDown(): void {
        $this->deleteDirectory($this->migrationsDirectory);
        $this->dbTearDown();
    }

    public static function migrationNames() {
        $dir = __DIR__ . "/expected";
        return [
            [
                "create_products_table",
                "$dir/create_products_table.php",
            ],
            [
                "add_category_to_products_table",
                "$dir/add_category_to_products_table.php",
            ],
            [
                "remove_name_from_products_table",
                "$dir/remove_name_from_products_table.php",
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('migrationNames')]
    public function test_creates_migration_files($name, $expectedMigrationFile) {
        $expectedName = sprintf("%s_%06d_%s.php", date('Y_m_d'), 0, $name);
        $this->migrator->make($name);
        $file = "$this->migrationsDirectory/$expectedName";
        $this->assertFileExists($file);
        $this->assertFileEquals($expectedMigrationFile, $file);
    }

    #[\PHPUnit\Framework\Attributes\Depends('test_creates_migration_files')]
    public function test_migrate_files() {
        $tables = ["users", "products", "sellers"];
        $migrated = [];

        foreach ($tables as $table) {
            $migrated[] = $this->migrator->make("create_{$table}_table");
        }

        $this->migrator->migrate();

        $rows = $this->driver->statement("SELECT * FROM migrations");

        $this->assertEquals(3, count($rows));
        $this->assertEquals($migrated, array_column($rows, "name"));

        foreach ($tables as $table) {
            try {
                $this->driver->statement("SELECT * FROM $table");
            } catch (PDOException $e) {
                $this->fail("Failed accesing migrated table: $table: {$e->getMessage()}");
            }
        }
    }

    #[\PHPUnit\Framework\Attributes\Depends('test_creates_migration_files')]
    public function test_rollback_files() {
        $tables = ["users", "products", "sellers", "providers", "referals"];
        $migrated = [];
        foreach ($tables as $table) {
            $migrated[] = $this->migrator->make("create_{$table}_table");
        }

        $this->migrator->migrate();

        // Rollback last migration
        $this->migrator->rollback(1);
        $rows = $this->driver->statement("SELECT * FROM migrations");
        $this->assertEquals(4, count($rows));
        $this->assertEquals(array_slice($migrated, 0, 4), array_column($rows, "name"));

        try {
            $table = $table[count($tables) - 1];
            $this->driver->statement("SELECT * FROM $table");
            $this->fail("Table $table was not deleted after rolling back");
        } catch (PDOException $e) {
            // OK
        }

        // Rollback another 2 migrationss
        $this->migrator->rollback(2);
        $rows = $this->driver->statement("SELECT * FROM migrations");
        $this->assertEquals(2, count($rows));
        $this->assertEquals(array_slice($migrated, 0, 2), array_column($rows, "name"));

        foreach (array_slice($tables, 2, 2) as $table) {
            try {
                $this->driver->statement("SELECT * FROM $table");
                $this->fail("Table '$table' was not deleted after rolling back");
            } catch (PDOException $e) {
                // OK
            }
        }

        // Rollback remainingAdd commentMore actions
        $this->migrator->rollback();
        $rows = $this->driver->statement("SELECT * FROM migrations");
        $this->assertEquals(0, count($rows));
    }
}
