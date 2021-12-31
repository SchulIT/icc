<?php

namespace App\Validator;

use App\Entity\DateLesson;
use App\Timetable\TimetableTimeHelper;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateLessonGreaterThanValidator extends ConstraintValidator {

    private TimetableTimeHelper $timetableTimeHelper;
    private TranslatorInterface $translator;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(TimetableTimeHelper $timeHelper, TranslatorInterface $translator, PropertyAccessorInterface $propertyAccessor) {
        $this->timetableTimeHelper = $timeHelper;
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
    }

    private function formatDate(DateLesson $lesson): string {
        return $lesson->getDate()->format($this->translator->trans('date.format_short'));
    }

    private function formatLesson(DateLesson $lesson): string {
        return $this->translator->trans('label.exam_lessons', [
            '%start%' => $lesson->getLesson(),
            '%count%' => 0
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function compareValues($value1, $value2): bool {
        $date1 = $this->timetableTimeHelper->getLessonEndDateTime($value1->getDate(), $value1->getLesson());
        $date2 = $this->timetableTimeHelper->getLessonStartDateTime($value2->getDate(), $value2->getLesson());

        return $date2 <= $date1;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$value instanceof DateLesson) {
            throw new UnexpectedTypeException($value, DateLesson::class);
        }

        if(!$constraint instanceof DateLessonGreaterThan) {
            throw new UnexpectedTypeException($constraint, DateLessonGreaterThan::class);
        }

        $path = $constraint->propertyPath;

        if(empty($path)) {
            throw new ConstraintDefinitionException('propertyPath must not be empty.');
        }

        $object = $this->context->getObject();

        try {
            /** @var DateLesson $comparedValue */
            $comparedValue = $this->propertyAccessor->getValue($object, $path);
        } catch (NoSuchPropertyException $e) {
            throw new ConstraintDefinitionException(sprintf('Invalid property path "%s" provided to "%s" constraint: ', $path, get_debug_type($constraint)).$e->getMessage(), 0, $e);
        }

        if($comparedValue->getDate() > $value->getDate()) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', $this->formatDate($comparedValue))
                //->atPath('date')
                ->addViolation();
        } else if($comparedValue->getDate() == $value->getDate() && $comparedValue->getLesson() > $value->getLesson()) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ compared_value }}', $this->formatLesson($comparedValue))
                //->atPath('lesson')
                ->addViolation();
        }
    }
}