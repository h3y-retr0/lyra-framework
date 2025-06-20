<?php

namespace Lyra\Tests\Validation;

use Lyra\Validation\Exceptions\ValidationException;
use Lyra\Validation\Rule;
use Lyra\Validation\Rules\Email;
use Lyra\Validation\Rules\LessThan;
use Lyra\Validation\Rules\Number;
use Lyra\Validation\Rules\Required;
use Lyra\Validation\Rules\RequiredWith;
use Lyra\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    protected function setUp(): void {
        Rule::loadDefaultRules();
    }

    public function test_basic_validation_passes() {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => new Email(),
            "other" => new Required(),
            "num" => new Number(),
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function test_basic_validatation_passes_with_strings() {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => "email",
            "other" => "required",
            "num" => "number",
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function test_throws_validation_exception_on_invalid_data() {
        $this->expectException(ValidationException::class);
        $v = new Validator(["test" => "test"]);
        $v->validate(["test" => new Number()]);
    }

    #[\PHPUnit\Framework\Attributes\Depends('test_basic_validation_passes')]
    public function test_multiple_rules_validation() {
        $data = ["age" => 20, "num" => 3, "foo" => 5];

        $rules = [
            "age" => new LessThan(100),
            "num" => [new RequiredWith("age"), new Number()],
        ];

        $expected = ["age" => 20, "num" => 3];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }


    public function test_returns_messages_for_each_rule_that_doesnt_pass() {
        $email = new Email();
        $required = new Required();
        $number = new Number();

        $data = ["email" => "test@", "num1" => "not a number"];

        $rules = [
            "email" => $email,
            "num1" => $number,
            "num2" => [$required, $number],
        ];

        $expected = [
            "email" => ["email" => $email->message()],
            "num1" => ["number" => $number->message()],
            "num2" => [
                "required" => $required->message(),
                "number" => $number->message()
            ],
        ];

        $v = new Validator($data);

        try {
            $v->validate($rules);
            $this->fail("Did not throw Validation Exception");
        } catch (ValidationException $e) {
            $this->assertEquals($expected, $e->errors());
        }
    }

    public function test_overrides_error_messages_correctly() {
        $data = ["email" => "test@", "num1" => "not a number"];

        $rules = [
            "email" => "email",
            "num1" => "number",
            "num2" =>  ["required", "number"],
        ];

        $messages = [
            "email" => ["email" => "test email message"],
            "num1" => ["number" => "test number message"],
            "num2" =>  [
                "required" => "test required message",
                "number" => "test number message again"
            ]
        ];

        $v = new Validator($data);

        try {
            $v->validate($rules, $messages);
            $this->fail("Did not throw ValidationException");
        } catch (ValidationException $e) {
            $this->assertEquals($messages, $e->errors());
        }
    }
}
