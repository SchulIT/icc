<?php

namespace App\Validator;

use App\Entity\LessonEntry;
use App\Repository\TimetableLessonRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LessonEntryMatchesTimetableValidator extends ConstraintValidator {

    private $lessonRepository;

    public function __construct(TimetableLessonRepositoryInterface $lessonRepository) {
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof LessonEntryMatchesTimetable) {
            throw new UnexpectedTypeException($constraint, LessonEntryMatchesTimetable::class);
        }

        if(!$value instanceof LessonEntry) {
            throw new UnexpectedTypeException($value, LessonEntry::class);
        }

        if($value->getTuition() === null || $value->getDate() === null) {
            return;
        }

        $lessons = $this->lessonRepository->findAllByTuitionsAndWeeks([$value->getTuition()], [ (int)$value->getDate()->format('W')]);

        foreach($lessons as $lesson) {
            if($value->getDate() < $lesson->getPeriod()->getStart() || $value->getDate() > $lesson->getPeriod()->getEnd()) { // period mismatch
                continue;
            }

            if($lesson->getDay() !== (int)$value->getDate()->format('N')) { // day mismatch
                continue;
            }

            $start = $lesson->getLesson();
            $end = $lesson->getLesson() + ($lesson->isDoubleLesson() ? 1 : 0);

            if($start <= $value->getLessonStart() && $value->getLessonEnd() <= $end) {
                return;
            }
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation();
    }
}