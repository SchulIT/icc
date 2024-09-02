<?php

namespace App\Validator;

use App\Entity\Section;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateInSectionValidator extends ConstraintValidator {

    public function __construct(private readonly PropertyAccessorInterface $propertyAccessor,
                                private readonly DateHelper $dateHelper,
                                private readonly TranslatorInterface $translator) {

    }

    public function validate(mixed $value, Constraint $constraint): void {
        if($value === null) {
            return;
        }

        if(!$value instanceof DateTime) {
            throw new UnexpectedTypeException($value, DateTime::class);
        }

        if(!$constraint instanceof DateInSection) {
            throw new UnexpectedTypeException($constraint, DateInSection::class);
        }

        $object = $this->context->getObject();
        $section = $this->propertyAccessor->getValue($object, $constraint->propertyPath);

        if($section === null) {
            return;
        }

        if(!$section instanceof Section) {
            throw new UnexpectedTypeException($section, Section::class);
        }

        if($this->dateHelper->isBetween($value, $section->getStart(), $section->getEnd()) !== true) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ start }}', $section->getStart()->format($this->translator->trans('date.format')))
                ->setParameter('{{ end }}', $section->getEnd()->format($this->translator->trans('date.format')))
                ->addViolation();
        }
    }
}