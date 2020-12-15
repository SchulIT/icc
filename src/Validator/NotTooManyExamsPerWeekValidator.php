<?php

namespace App\Validator;

use App\Entity\Exam;
use App\Entity\Student;
use App\Repository\ExamRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotTooManyExamsPerWeekValidator extends AbstractExamConstraintValidator {

    private $examSettings;
    private $authorizationChecker;

    public function __construct(ExamSettings $examSettings, ExamRepositoryInterface $examRepository, AuthorizationCheckerInterface $authorizationChecker) {
        parent::__construct($examRepository);

        $this->examSettings = $examSettings;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$value instanceof Exam) {
            throw new UnexpectedTypeException($value, Exam::class);
        }

        if(!$constraint instanceof NotTooManyExamsPerWeek) {
            throw new UnexpectedTypeException($constraint, NotTooManyExamsPerWeek::class);
        }

        if($value->getDate() === null || $value->getTuitions()->count() === 0) {
            // Planned exams are fine
            return true;
        }

        $examWeek = $value->getDate()->format('W');

        /** @var Student $student */
        foreach($value->getStudents() as $student) {
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

            if($numberOfExams > $this->examSettings->getMaximumNumberOfExamsPerWeek($student->getGrade())) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ maxNumber }}', (string)$this->examSettings->getMaximumNumberOfExamsPerWeek($student->getGrade()))
                    ->setParameter('{{ number }}', (string)$numberOfExams)
                    ->addViolation();
            }
        }
    }
}