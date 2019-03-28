<?php

namespace App\Tests\Validator;

use App\Validator\NullOrNotBlank;
use App\Validator\NullOrNotBlankValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NullOrNotBlankValidatorTest extends ConstraintValidatorTestCase {

    public function getValidValues() {
        return [
            ['abc'],
            ['null'],
            [null]
        ];
    }

    public function getInvalidValues() {
        return [
            [''],
            [""],
            [false]
        ];
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues($value) {
        $constraint = new NullOrNotBlank();
        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues($value) {
        $constraint = new NullOrNotBlank();
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->assertRaised();
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testInvalidConstraint() {
        $constraint = new NotBlank();
        $this->validator->validate(null, $constraint);
    }

    protected function createValidator() {
        return new NullOrNotBlankValidator();
    }
}