<?php

namespace App\Validator;

use App\Entity\Section;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\TimetableLessonData;
use App\Section\SectionResolverInterface;
use App\Settings\ImportSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TuitionResolvableValidator extends ConstraintValidator {

    private array $cache = [ ];

    public function __construct(private readonly SectionResolverInterface $sectionResolver,
                                private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly ImportSettings $importSettings) { }

    public function validate(mixed $value, Constraint $constraint): void {
        if(!$value instanceof TimetableLessonData) {
            throw new UnexpectedTypeException($value, TimetableLessonData::class);
        }

        if(!$constraint instanceof TuitionResolvable) {
            throw new UnexpectedTypeException($constraint, TuitionResolvable::class);
        }

        if(empty($value->getSubject()) || count($value->getGrades()) === 0 || count($value->getTeachers()) === 0) {
            return;
        }

        if(in_array($value->getSubject(), $this->importSettings->getSubjectsWithoutTuition())) {
            return;
        }

        $section = $this->sectionResolver->getSectionForDate($value->getDate());
        $tuition = $this->findTuition($value->getGrades(), $value->getTeachers(), $value->getSubject(), $section);



        if(count($tuition) === 0) {
            $this->context
                ->buildViolation($constraint->notMatchedmessage)
                ->setParameter('{{ subject }}', $value->getSubject())
                ->setParameter('{{ teachers }}', implode(', ', $value->getTeachers()))
                ->setParameter('{{ grades }}', implode(', ', $value->getGrades()))
                ->addViolation();
        }

        if(count($tuition) > 1) {
            $this->context
                ->buildViolation($constraint->ambiguousMessage)
                ->setParameter('{{ subject }}', $value->getSubject())
                ->setParameter('{{ teachers }}', implode(', ', $value->getTeachers()))
                ->setParameter('{{ grades }}', implode(', ', $value->getGrades()))
                ->addViolation();
        }

        // tuition resolved :)
    }

    /**
     * @param string[] $grades
     * @param string[] $teachers
     * @return Tuition[]
     */
    private function findTuition(array $grades, array $teachers, string $subjectOrCourse, Section $section): array {
        sort($grades);
        sort($teachers);

        $key = sprintf(
            '%d-%s-%s-%s',
            $section->getId(),
            implode('~', $grades),
            implode('~', $teachers),
            $subjectOrCourse
        );

        if(!isset($this->cache[$key])) {
            $this->cache[$key] = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($grades, $teachers, $subjectOrCourse, $section);
        }

        return $this->cache[$key];
    }
}