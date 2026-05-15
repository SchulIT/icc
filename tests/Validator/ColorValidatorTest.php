<?php

namespace App\Tests\Validator;

use App\Framework\Validator\Color;
use App\Framework\Validator\ColorValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ColorValidatorTest extends ConstraintValidatorTestCase {
    public static function getValidColors(): array {
        return [
            ['#000000'],
            ['#ABCDEF'],
            ['#123ABC'],
            ['#abcDEF'],
            ['#123aBC']
        ];
    }

    public static function getInvalidColors(): array {
        return [
            ['000'],
            ['defghi'],
            ['abcDEF']
        ];
    }

    public function testInvalidConstraint() {
        $this->expectException(UnexpectedTypeException::class);
        $constraint = new NotBlank();
        $this->validator->validate(null, $constraint);
    }

    #[DataProvider('getValidColors')]
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

    #[DataProvider('getInvalidColors')]
    public function testInvalidColors($color) {
        $constraint = new Color();

        $this->validator->validate($color, $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $color)
            ->assertRaised();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new ColorValidator();
    }
}