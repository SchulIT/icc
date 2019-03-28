<?php

namespace App\Tests\Validator;

use App\Validator\Color;
use App\Validator\ColorValidator;
use App\Validator\NullOrNotBlank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ColorValidatorTest extends ConstraintValidatorTestCase {
    public function getValidColors() {
        return [
            ['000000'],
            ['ABCDEF'],
            ['123ABC'],
            ['abcDEF'],
            ['123aBC']
        ];
    }

    public function getInvalidColors() {
        return [
            ['000'],
            ['defghi']
        ];
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testInvalidConstraint() {
        $constraint = new NotBlank();
        $this->validator->validate(null, $constraint);
    }

    /**
     * @dataProvider getValidColors
     */
    public function testValidColors($color) {
        $constraint = new Color();

        $this->validator->validate($color, $constraint);
        $this->assertNoViolation();
    }

    public function testNullIsValidColor() {
        $constraint = new Color();

        $this->validator->validate(null, $constraint);
        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidColors
     */
    public function testInvalidColors($color) {
        $constraint = new Color();

        $this->validator->validate($color, $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $color)
            ->assertRaised();
    }

    protected function createValidator() {
        return new ColorValidator();
    }
}