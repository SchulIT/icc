<?php

namespace App\Validator;

use App\Entity\Exam;
use App\Repository\ExamRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotTooManyExamsPerDayValidator extends ConstraintValidator {

    private $examSettings;
    private $examRepository;

    public function __construct(ExamSettings $examSettings, ExamRepositoryInterface $examRepository) {
        $this->examSettings = $examSettings;
        $this->examRepository = $examRepository;
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

        $exams = $this->examRepository->findAllByTuitions($value->getTuitions()->toArray());
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
                ->setParameter('{{ maxNumber }}', $this->examSettings->getMaximumNumberOfExamsPerDay())
                ->setParameter('{{ number }}', $numberOfExams)
                ->addViolation();
        }
    }
}