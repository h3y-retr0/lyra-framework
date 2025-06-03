<?php

namespace Lyra\Validation;

use Lyra\Validation\Rules\Email;
use Lyra\Validation\Rules\LessThan;
use Lyra\Validation\Rules\Number;
use Lyra\Validation\Rules\Required;
use Lyra\Validation\Rules\RequiredWhen;
use Lyra\Validation\Rules\RequiredWith;
use Lyra\Validation\Rules\ValidationRule;

class Rule {
    public static function email(): ValidationRule {
        return new Email();
    }

    public static function required(): ValidationRule {
        return new Required();
    }

    public function requiredWith(string $withField): ValidationRule {
        return new RequiredWith($withField);
    }

    public static function number(): ValidationRule {
        return new Number();
    }

    public static function lessThan(int|float $value): ValidationRule {
        return new LessThan($value);
    }

    public static function requiredWhen(
        string $otherField,
        string $operator,
        int|float $value
    ): ValidationRule {
        return new RequiredWhen($otherField, $operator, $value);
    }
}
