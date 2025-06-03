<?php

namespace Lyra\Validation\Exceptions;

use Lyra\Exceptions\LyraException;

class ValidationException extends LyraException {
    public function __construct(protected array $errors) {
        $this->errors = $errors;
    }

    public function errors(): array {
        return $this->errors;
    }
}
