<?php

namespace Lyra\Validation\Rules;

use Lyra\Validation\Exceptions\RuleParseException;

class RequiredWhen implements ValidationRule {
    /**
     * Instanciate required when rule.
     *
     * @param string $otherField Field used for comparation
     * @param string $operator Operator to use for the if statement
     * @param string $compareWit Value to compare with using `$operator`
     */
    public function __construct(
        private string $otherField,
        private string $operator,
        private string $compareWith,
    ) {
        $this->otherField = $otherField;
        $this->operator = $operator;
        $this->compareWith = $compareWith;
    }

    public function message(): string {
        return "This field is required when {$this->otherField} {$this->operator} {$this->compareWith}";
    }

    public function isValid(string $field, array $data): bool {
        if (!array_key_exists($this->otherField, $data)) {
            return false;
        }

        $isRequired = match ($this->operator) {
            "=" => $data[$this->otherField] == $this->compareWith,
            ">" => $data[$this->otherField] > floatval($this->compareWith),
            "<" => $data[$this->otherField] < floatval($this->compareWith),
            ">=" => $data[$this->otherField] >= floatval($this->compareWith),
            "<=" => $data[$this->otherField] <= floatval($this->compareWith),
            default => throw new RuleParseException("Unknown required_when operator: $this->operator")
        };

        return !$isRequired || isset($data[$field]) && $data[$field] != "";
    }
}
