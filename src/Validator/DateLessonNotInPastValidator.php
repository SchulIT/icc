<?php

namespace App\Validator;

use App\Entity\DateLesson;
use App\Entity\StudentAbsence;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateLessonNotInPastValidator extends ConstraintValidator {

    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker, private readonly DateHelper $dateHelper,
                                private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$value instanceof DateLesson) {
            throw new UnexpectedTypeException($value, DateLesson::class);
        }

        if(!$constraint instanceof DateLessonNotInPast) {
            throw new UnexpectedTypeException($constraint, DateLessonNotInPast::class);
        }

        $exceptions = $constraint->exceptions;

        foreach($exceptions as $role) {
            if($this->authorizationChecker->isGranted($role)) {
                return;
            }
        }

        if($constraint->propertyName !== null) {
            $parentObject = $this->context->getObject();

            if ($parentObject instanceof StudentAbsence) {
                $oldEntity = $this->em
                    ->getUnitOfWork()
                    ->getOriginalEntityData($parentObject);

                if(array_key_exists($constraint->propertyName . '.date', $oldEntity)) {
                    $oldDate = $oldEntity[$constraint->propertyName . '.date'];

                    if ($oldDate == $value->getDate()) {
                        // date was not changed -> disable validation
                        return;
                    }
                }
            }
        }

        $today = $this->dateHelper->getToday();

        if($value->getDate() < $today) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}