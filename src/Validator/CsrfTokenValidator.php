<?php

namespace App\Validator;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CsrfTokenValidator extends ConstraintValidator {

    public function __construct(private CsrfTokenManagerInterface $tokenManager)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof CsrfToken) {
            throw new UnexpectedTypeException($constraint, CsrfToken::class);
        }

        if($this->tokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken($constraint->id, $value)) !== true) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}