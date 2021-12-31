<?php

namespace App\Validator;

use DateTimeInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateIsNotInPastValidator extends ConstraintValidator {

    private DateHelper $dateHelper;

    public function __construct(DateHelper $dateHelper) {
        $this->dateHelper = $dateHelper;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof DateIsNotInPast) {
            throw new UnexpectedTypeException($constraint, DateIsNotInPast::class);
        }

        if(!$value instanceof DateTimeInterface) {
            throw new UnexpectedTypeException($value, DateTimeInterface::class);
        }

        $today = $this->dateHelper->getToday();

        if($value < $today) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}