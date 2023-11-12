<?php

namespace App\Validator;

use App\Converter\StudentStringConverter;
use App\Entity\Exam;
use App\Repository\ExamRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotTooManyExamsPerDayValidator extends AbstractExamConstraintValidator {

    public function __construct(private readonly ExamSettings $examSettings, private readonly StudentStringConverter $studentStringConverter, ExamRepositoryInterface $examRepository) {
        parent::__construct($examRepository);
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$value instanceof Exam) {
            throw new UnexpectedTypeException($value, Exam::class);
        }

        if(!$constraint instanceof NotTooManyExamsPerDay) {
            throw new UnexpectedTypeException($constraint, NotTooManyExamsPerDay::class);
        }

        if($value->getDate() === null || $value->getTuitions()->count() === 0) {
            // Planned exams are fine
            return;
        }

        foreach($value->getStudents() as $student) {
            $exams = $this->findAllByStudent($student);
            $numberOfExams = 1;

            foreach ($exams as $existingExam) {
                if ($existingExam->getId() === $value->getId() || $existingExam->getDate() === null) {
                    continue;
                }

                if ($existingExam->getDate() == $value->getDate()) {
                    $numberOfExams++;
                }
            }

            if ($numberOfExams > $this->examSettings->getMaximumNumberOfExamsPerDay()) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ student }}', $this->studentStringConverter->convert($student))
                    ->setParameter('{{ maxNumber }}', (string)$this->examSettings->getMaximumNumberOfExamsPerDay())
                    ->setParameter('{{ number }}', (string)$numberOfExams)
                    ->addViolation();
            }
        }
    }
}