<?php

namespace App\Validator;

use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotInTheFutureValidator extends ConstraintValidator {

    private $dateHelper;

    public function __construct(DateHelper $dateHelper) {
        $this->dateHelper = $dateHelper;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof NotInTheFuture) {
            throw new UnexpectedTypeException($constraint, NotInTheFuture::class);
        }

        if(!$value instanceof DateTime) {
            throw new UnexpectedTypeException($value, DateTime::class);
        }

        if($value > $this->dateHelper->getToday()) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}