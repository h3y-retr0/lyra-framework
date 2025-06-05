<?php

namespace Lyra\Validation;

use Lyra\Validation\Exceptions\RuleParseException;
use Lyra\Validation\Exceptions\UnknownRuleException;
use Lyra\Validation\Rules\Email;
use Lyra\Validation\Rules\LessThan;
use Lyra\Validation\Rules\Number;
use Lyra\Validation\Rules\Required;
use Lyra\Validation\Rules\RequiredWhen;
use Lyra\Validation\Rules\RequiredWith;
use Lyra\Validation\Rules\ValidationRule;
use ReflectionClass;

class Rule {
    private static array $rules = [];

    private static array $defaultRules = [
        Required::class,
        RequiredWith::class,
        RequiredWhen::class,
        Number::class,
        LessThan::class,
        Email::class,
    ];

    public static function loadDefaultRules() {
        // 'required_when' => \Lyra\Validation\Rules\RequiredWhen
        self::load(self::$defaultRules);
    }

    public static function load(array $rules) {
        // $rule is something like \Lyra\Validation\Rules\x => we convert it into [x]
        foreach ($rules as $class) {
            $className = array_slice(explode("\\", $class), -1)[0];
            $ruleName = snake_case($className);
            self::$rules[$ruleName] = $class;
        }
    }

    public static function nameOf(ValidationRule $rule): string {
        $class = new ReflectionClass($rule);

        return snake_case($class->getShortName());
    }

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

    public static function parseBasicRule(string $ruleName): ValidationRule {
        $class = new ReflectionClass(self::$rules[$ruleName]);

        if (count($class->getConstructor()?->getParameters() ?? []) > 0) {
            throw new RuleParseException("Rule $ruleName requires parameters, but none were given");
        }

        return $class->newInstance();
    }

    public static function parseRuleWithParameters(string $ruleName, string $params): ValidationRule {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $constructorParams = $class->getConstructor()?->getParameters() ?? [];
        $givenParams = array_filter(explode(",", $params), fn ($p) => !empty($p));

        if (count($givenParams) !== count($constructorParams)) {
            throw new RuleParseException(sprintf(
                "Rule %s requires %d parameters, but %d were given: %s",
                $ruleName,
                count($constructorParams),
                count($givenParams),
                $params
            ));
        }

        return $class->newInstance(...$givenParams);
    }

    public static function from(string $str): ValidationRule {
        // TODO: parse rules as Laravel does: 'email' => 'required|email'
        /**
         * Rules format:
         * Required -> required
         * LessThan(5) -> less_than:5
         * RequiredWhen(num, >=, 6) -> required_when:num,>=,6
         */

        if (strlen($str) == 0) {
            throw new RuleParseException("Can't parse empty string to rule");
        }

        $ruleParts = explode(":", $str);

        if (!array_key_exists($ruleParts[0], self::$rules)) {
            throw new UnknownRuleException("Rule {$ruleParts[0]} not found");
        }

        if (count($ruleParts) == 1) {
            return self::parseBasicRule($ruleParts[0]);
        }

        [$ruleName, $params] = $ruleParts;

        return self::parseRuleWithParameters($ruleName, $params);
    }
}
