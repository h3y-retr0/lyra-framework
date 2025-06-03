<?php

namespace Lyra\Validation\Rules;

interface ValidationRule {
    /**
     * Invalid rule message.
     *
     * @return string
     */
    public function message(): string;

    /**
     * Check if a field is valid.
     *
     * @param string $field
     * @param array $data
     * @return boolean
     */
    public function isValid(string $field, array $data): bool;
}
