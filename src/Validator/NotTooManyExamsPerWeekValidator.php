<?php

namespace App\Validator;

use App\Entity\Exam;
use App\Repository\ExamRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotTooManyExamsPerWeekValidator extends ConstraintValidator {

    private $examSettings;
    private $examRepository;
    private $authorizationChecker;

    public function __construct(ExamSettings $examSettings, ExamRepositoryInterface $examRepository, AuthorizationCheckerInterface $authorizationChecker) {
        $this->examSettings = $examSettings;
        $this->examRepository = $examRepository;
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

        if($this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR')) {
            // Users with ROLE_EXAMS_CREATOR can override those rules
            return true;
        }

        if($value->getDate() === null || $value->getTuitions()->count() === 0) {
            // Planned exams are fine
            return true;
        }

        $examWeek = $value->getDate()->format('W');

        $exams = $this->examRepository->findAllByTuitions($value->getTuitions()->toArray());
        $numberOfExams = 1;

        foreach($exams as $existingExam) {
            if($existingExam->getId() === $value->getId() || $existingExam->getDate() === null) {
                continue;
            }

            if($existingExam->getDate()->format('W') === $examWeek) {
                $numberOfExams++;
            }
        }

        if($numberOfExams > $this->examSettings->getMaximumNumberOfExamsPerWeek()) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ maxNumber }}', $this->examSettings->getMaximumNumberOfExamsPerWeek())
                ->setParameter('{{ number }}', $numberOfExams)
                ->addViolation();
        }
    }
}