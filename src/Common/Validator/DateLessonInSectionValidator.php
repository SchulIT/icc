<?php

namespace App\Common\Validator;

use App\Common\Entity\DateLesson;
use App\Common\Entity\Section;
use App\Common\Repository\SectionRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Common\Validator\DateLessonInSection;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateLessonInSectionValidator extends ConstraintValidator {

    public function __construct(private readonly SectionResolverInterface $sectionResolver, private readonly SectionRepositoryInterface $sectionRepository) { }

    public function validate(mixed $value, Constraint $constraint): void {
        if(!$value instanceof DateLesson) {
            throw new UnexpectedTypeException($value, DateLesson::class);
        }

        if(!$constraint instanceof DateLessonInSection) {
            throw new UnexpectedTypeException($constraint, DateLessonInSection::class);
        }

        $section = $this->sectionResolver->getSectionForDate($value->getDate());

        if($section === null) {
            $sections = $this->sectionRepository->findAll();

            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ sections }}', implode(', ', array_map(fn(Section $section) => $section->getDisplayName(), $sections)))
                ->addViolation();
        }
    }
}