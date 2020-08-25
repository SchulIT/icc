<?php

namespace App\Validator;

use App\Entity\Exam;
use App\Repository\ExamRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotTooManyExamsPerDayValidator extends AbstractExamConstraintValidator {

    private $examSettings;


    public function __construct(ExamSettings $examSettings, ExamRepositoryInterface $examRepository) {
        parent::__construct($examRepository);

        $this->examSettings = $examSettings;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$value instanceof Exam) {
            throw new UnexpectedTypeException($value, Exam::class);
        }

        if(!$constraint instanceof NotTooManyExamsPerDay) {
            throw new UnexpectedTypeException($constraint, NotTooManyExamsPerDay::class);
        }

        if($value->getDate() === null || $value->getTuitions()->count() === 0) {
            // Planned exams are fine
            return true;
        }

        $exams = $this->findAllByStudents($value->getStudents()->toArray());
        $numberOfExams = 1;

        foreach($exams as $existingExam) {
            if($existingExam->getId() === $value->getId() || $existingExam->getDate() === null) {
                continue;
            }

            if($existingExam->getDate() == $value->getDate()) {
                $numberOfExams++;
            }
        }

        if($numberOfExams > $this->examSettings->getMaximumNumberOfExamsPerDay()) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ maxNumber }}', (string)$this->examSettings->getMaximumNumberOfExamsPerDay())
                ->setParameter('{{ number }}', (string)$numberOfExams)
                ->addViolation();
        }
    }
}