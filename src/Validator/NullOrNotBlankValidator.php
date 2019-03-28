<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NullOrNotBlankValidator extends ConstraintValidator {

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof NullOrNotBlank) {
            throw new UnexpectedTypeException($constraint, NullOrNotBlank::class);
        }

        if($value !== null && (false === $value || (empty($value) && '0' != $value))) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}