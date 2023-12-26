<?php

namespace App\Validator;

use App\Converter\StudentStringConverter;
use App\Entity\Exam;
use App\Entity\Student;
use App\Repository\ExamRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\ExamSettings;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotTooManyExamsPerWeekValidator extends AbstractExamConstraintValidator {

    public function __construct(private readonly ExamSettings $examSettings, private readonly StudentStringConverter $studentStringConverter, ExamRepositoryInterface $examRepository, private readonly SectionResolverInterface $sectionResolver) {
        parent::__construct($examRepository);
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$value instanceof Exam) {
            throw new UnexpectedTypeException($value, Exam::class);
        }

        if(!$constraint instanceof NotTooManyExamsPerWeek) {
            throw new UnexpectedTypeException($constraint, NotTooManyExamsPerWeek::class);
        }

        if($value->getDate() === null || $value->getTuitions()->count() === 0) {
            // Planned exams are fine
            return;
        }

        $examWeek = $value->getDate()->format('W');

        foreach($value->getStudents() as $examStudent) {
            $student = $examStudent->getStudent();
            $numberOfExams = 1;
            $exams = $this->findAllByStudent($student);

            foreach($exams as $existingExam) {
                if($existingExam->getId() === $value->getId() || $existingExam->getDate() === null) {
                    continue;
                }

                if($existingExam->getDate()->format('W') === $examWeek) {
                    $numberOfExams++;
                }
            }

            $section = $this->sectionResolver->getSectionForDate($value->getDate());
            if($numberOfExams > $this->examSettings->getMaximumNumberOfExamsPerWeek($student->getGrade($section))) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ student }}', $this->studentStringConverter->convert($student))
                    ->setParameter('{{ maxNumber }}', (string)$this->examSettings->getMaximumNumberOfExamsPerWeek($student->getGrade($section)))
                    ->setParameter('{{ number }}', (string)$numberOfExams)
                    ->addViolation();
            }
        }
    }
}