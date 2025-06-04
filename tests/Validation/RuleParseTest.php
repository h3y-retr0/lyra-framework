<?php

namespace Lyra\Tests\Validation;

use Lyra\Validation\Exceptions\RuleParseException;
use Lyra\Validation\Exceptions\UnknownRuleException;
use Lyra\Validation\Rule;
use Lyra\Validation\Rules\Email;
use Lyra\Validation\Rules\LessThan;
use Lyra\Validation\Rules\Number;
use Lyra\Validation\Rules\Required;
use Lyra\Validation\Rules\RequiredWhen;
use Lyra\Validation\Rules\RequiredWith;
use PHPUnit\Framework\TestCase;

class RuleParseTest extends TestCase {
    protected function setUp(): void {
        Rule::loadDefaultRules();
    }

    public static function basicRules() {
        return [
            [Email::class, "email"],
            [Required::class, "required"],
            [Number::class, "number"],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('basicRules')]
    public function test_parse_basic_rules($class, $name)  {
        $this->assertInstanceOf($class, Rule::from($name));
    }

    public function test_parsing_unknown_rules_throws_unkown_rule_exception() {
        $this->expectException(UnknownRuleException::class);
        Rule::from("unknown");
    }

    public static function rulesWithParameters() {
        return [
            [new LessThan(5), "less_than:5"],
            [new RequiredWith("other"), "required_with:other"],
            [new RequiredWhen("other", "=", "test"), "required_when:other,=,test"],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rulesWithParameters')]
    public function test_parse_rules_with_parameters($expected, $rule) {
        $this->assertEquals($expected, Rule::from($rule));
    }

    public static function rulesWithParametersWithError() {
        return [
            ["less_than"],
            ["less_than:"],
            ["required_with:"],
            ["required_when"],
            ["required_when:"],
            ["required_when:other"],
            ["required_when:other,"],
            ["required_when:other,="],
            ["required_when:other,=,"],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rulesWithParametersWithError')]
    public function test_parsing_rule_with_parameters_without_passing_correct_parameters_throws_rule_parse_exception($rule) {
        $this->expectException(RuleParseException::class);
        Rule::from($rule);
    }
}
