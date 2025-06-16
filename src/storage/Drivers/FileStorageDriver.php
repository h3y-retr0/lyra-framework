<?php

namespace Lyra\storage\Drivers;

/**
 * File storage driver.
 */
interface FileStorageDriver {
    /**
     * Store file.
     *
     * @param string $path
     * @param mixed $content
     * @return string
     */
    public function put(string $path, mixed $content): string;
}
