<?php

namespace App\Validator;

use App\Utils\ArrayUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueArrayValidator extends ConstraintValidator {

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if(!$constraint instanceof UniqueArray) {
            throw new UnexpectedTypeException($constraint, UniqueArray::class);
        }

        $unique = ArrayUtils::unique($value);

        if(count($value) !== count($unique)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}