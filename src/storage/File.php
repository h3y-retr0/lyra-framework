<?php

namespace Lyra\storage;

/**
 * File helper.
 */
class File {
    /**
     * Instanciate new file.
     *
     * @param mixed $content
     * @param string $type
     * @param string $originalName
     */
    public function __construct(
        private mixed $content,
        private string $type,
        private string $originalName
    ) {
       $this->content = $content;
       $this->type = $type;
       $this->originalName = $originalName; 
    }

    /**
     * Check if the current file is an image.
     *
     * @return boolean
     */
    public function isImg(): bool {
        return str_starts_with($this->type, "image");
    }

    /**
     * Type of the image.
     *
     * @return string|null
     */
    public function extension(): ?string {
        return match($this->type) {
            "image/jpeg" => "jpeg",
            "image/png" => "png",
            "application/pdf" => "pdf",
            default => null,
        };
    }

    public function strore(?string $directory = null): string {
        $file = uniqid() . $this->extension();
        $path = is_null($directory) ? $file: "$directory/$file";
        return Storage::put($path, $this->content);
    }


}
