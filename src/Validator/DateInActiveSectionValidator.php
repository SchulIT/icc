<?php

namespace App\Validator;

use App\Section\SectionResolverInterface;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateInActiveSectionValidator extends ConstraintValidator {

    public function __construct(private DateHelper $dateHelper, private SectionResolverInterface $sectionResolver, private TranslatorInterface $translator)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$value instanceof DateTime) {
            throw new UnexpectedTypeException($value, DateTime::class);
        }

        if(!$constraint instanceof DateInActiveSection) {
            throw new UnexpectedTypeException($constraint, DateInActiveSection::class);
        }

        $section = $this->sectionResolver->getCurrentSection();

        if($section === null) {
            return;
        }

        if($this->dateHelper->isBetween($value, $section->getStart(), $section->getEnd()) !== true) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ start }}', $section->getStart()->format($this->translator->trans('date.format')))
                ->setParameter('{{ end }}', $section->getEnd()->format($this->translator->trans('date.format')))
                ->addViolation();
        }
    }
}