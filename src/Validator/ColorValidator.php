<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ColorValidator extends ConstraintValidator {

    static $ColorRegexp = '/^([a-f0-9]){6}$/';

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof Color) {
            throw new UnexpectedTypeException($constraint, Color::class);
        }

        if(!empty($value) && !preg_match(static::$ColorRegexp, $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}