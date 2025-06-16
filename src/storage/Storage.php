<?php

namespace Lyra\storage;

/**
 * File storage utilities.
 */
class Storage {
    /**
     * Put file in the storage directory.
     *
     * @param string $path
     * @param mixed $content
     * @return string
     */
    public static function put(string $path, mixed $content): string {
        return app(FileStorageDriver::class)->put($path, $content);
    }
}
