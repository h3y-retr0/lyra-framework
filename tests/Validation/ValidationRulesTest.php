<?php

namespace Lyra\Tests\Validation;

use Lyra\Validation\Rules\Email;
use Lyra\Validation\Rules\LessThan;
use Lyra\Validation\Rules\Number;
use Lyra\Validation\Rules\Required;
use Lyra\Validation\Rules\RequiredWhen;
use Lyra\Validation\Rules\RequiredWith;
use PHPUnit\Framework\TestCase;

class ValidationRulesTest extends TestCase {
    public static function emails() {
        return [
            ["test@test.com", true],
            ["manuel@mastermind.ac", true],
            ["test@testcom", false],
            ["test@test.", false],
            ["manuel@", false],
            ["manuel@.", false],
            ["manuel", false],
            ["@", false],
            ["", false],
            [4, false],
        ];
    }


    public static function requiredData() {
        return [
            ["", false],
            [5, true],
            [null, false],
            ["test", true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('emails')]
    public function test_email($email, $expected) {
        $data = ['email' => $email];
        $rule = new Email();
        $this->assertEquals($expected, $rule->isValid('email', $data));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('requiredData')]
    public function test_required($value, $expected) {
        $data = ['test' => $value];
        $rule = new Required();
        $this->assertEquals($expected, $rule->isValid('test', $data));
    }

    public function test_required_with() {
        $rule = new RequiredWith('other');
        $data = ['other' => 10, 'test' => 5];
        $this->assertTrue($rule->isValid('test', $data));
        $data = ['other' => 10];
        $this->assertFalse($rule->isValid('test', $data));
    }

    public static function lessThanData() {
        return [
            [5, 5, false],
            [5, 6, false],
            [5, 3, true],
            [5, null, false],
            [5, "", false],
            [5, "test", false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('lessThanData')]
    public function test_less_than($value, $check, $expected) {
        $rule = new LessThan($value);
        $data = ["test" => $check];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }

    public static function numbers() {
        return [
            [0, true],
            [1, true],
            [1.5, true],
            [-1, true],
            [-1.5, true],
            ["0", true],
            ["1", true],
            ["1.5", true],
            ["-1", true],
            ["-1.5", true],
            ["test", false],
            ["1test", false],
            ["-5test", false],
            ["", false],
            [null, false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('numbers')]
    public function test_number($n, $expected) {
        $rule = new Number();
        $data = ["test" => $n];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }

    public static function requiredWhenData() {
        return [
            ["other", "=", "value", ["other" => "value"], "test", false],
            ["other", "=", "value", ["other" => "value", "test" => 1], "test", true],
            ["other", "=", "value", ["other" => "not value"], "test", true],
            ["other", ">", 5, ["other" => 1], "test", true],
            ["other", ">", 5, ["other" => 6], "test", false],
            ["other", ">", 5, ["other" => 6, "test" => 1], "test", true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('requiredWhenData')]
    public function test_required_when($other, $operator, $compareWith, $data, $field, $expected) {
        $rule = new RequiredWhen($other, $operator, $compareWith);
        $this->assertEquals($expected, $rule->isValid($field, $data));
    }
}
