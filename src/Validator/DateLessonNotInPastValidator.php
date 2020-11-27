<?php

namespace App\Validator;

use App\Entity\DateLesson;
use App\Timetable\TimetableTimeHelper;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateLessonNotInPastValidator extends ConstraintValidator {

    private $authorizationChecker;
    private $dateHelper;
    private $timetableLessonHelper;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, DateHelper $dateHelper, TimetableTimeHelper $timeHelper) {
        $this->authorizationChecker = $authorizationChecker;
        $this->dateHelper = $dateHelper;
        $this->timetableLessonHelper = $timeHelper;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$value instanceof DateLesson) {
            throw new UnexpectedTypeException($value, DateLesson::class);
        }

        if(!$constraint instanceof DateLessonNotInPast) {
            throw new UnexpectedTypeException($constraint, DateLessonNotInPast::class);
        }

        /*if($this->authorizationChecker->isGranted('ROLE_SICK_NOTE_CREATOR')) {
            return;
        }*/

        $today = $this->dateHelper->getToday();

        if($value->getDate() < $today) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}