<?php

namespace App\Exam\Validator;

use App\Common\Converter\StudentStringConverter;
use App\Exam\Entity\Exam;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Exam\Validator\NotTooManyExamsPerDay;
use App\Exam\Settings\ExamSettings;
use App\Exam\Validator\AbstractExamConstraintValidator;
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

        foreach($value->getStudents() as $examStudent) {
            $student = $examStudent->getStudent();
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