<?php

namespace Lyra\Tests\Storage;

use Lyra\Storage\Drivers\DiskFileStorage;
use PHPUnit\Framework\TestCase;

class DiskFileStorageTest extends TestCase {
    protected $storageDirectory = __DIR__ . "/test-storage";


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

    protected function removeTestStorageDirectory() {
        if (file_exists($this->storageDirectory)) {
            // shell_exec("rm -r '$this->storageDirectory'");
            $this->deleteDirectory($this->storageDirectory);
        }
    }

    protected function setUp(): void {
        $this->removeTestStorageDirectory();
    }

    protected function tearDown(): void {
        $this->removeTestStorageDirectory();
    }

    public static function files() {
        return [
            ["test.txt", "Hello World"],
            ["test/test.txt", "Hello World"],
            ["test/subdir/longer/dir/test.txt", "Hello World"],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('files')]
    public function testStoresSingleFileAndCreatesParentDirectories($file, $content) {
        $appUrl = "localhost:8080";
        $storageUri = "storage";
        $storage = new DiskFileStorage($this->storageDirectory, $storageUri, $appUrl);
        $url = $storage->put($file, $content);
        $path = "$this->storageDirectory/$file";

        $this->assertDirectoryExists($this->storageDirectory);
        $this->assertFileExists($path);
        $this->assertEquals($content, file_get_contents($path));
        $this->assertEquals("$appUrl/$storageUri/$file", $url);
    }

    public function testStoresMultipleFiles() {
        $f1 = "test.txt";
        $f2 = "f2.txt";
        $f3 = "foo/bar/f3.txt";
        $storage = new DiskFileStorage($this->storageDirectory, "test", "test");

        foreach ([$f1, $f2, $f3] as $f) {
            $storage->put($f, $f);
        }

        foreach ([$f1, $f2, $f3] as $f) {
            $this->assertFileExists("$this->storageDirectory/$f");
            $this->assertEquals($f, file_get_contents("$this->storageDirectory/$f"));
        }
    }
}
