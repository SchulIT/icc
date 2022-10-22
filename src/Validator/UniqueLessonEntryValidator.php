<?php

namespace App\Validator;

use App\Entity\LessonEntry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueLessonEntryValidator extends ConstraintValidator {

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof UniqueLessonEntry) {
            throw new UnexpectedTypeException($value, UniqueLessonEntry::class);
        }

        if(!$value instanceof LessonEntry) {
            throw new UnexpectedTypeException($value, LessonEntry::class);
        }

        if($value->getLesson() === null) {
            return;
        }

        $entries = $value->getLesson()->getEntries();
        $valueLessons = $this->getLessonsForEntry($value);

        foreach($entries as $entry) {
            if($value->getId() === $entry->getId()) { // do not compare with same entity
                continue;
            }

            $entryLessons = $this->getLessonsForEntry($entry);
            $intersect = array_intersect($valueLessons, $entryLessons);

            if(count($intersect) > 0) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();

                return;
            }
        }
    }

    /**
     * @return int[]
     */
    private function getLessonsForEntry(LessonEntry $entry): array {
        $lessons = [ ];

        for($lesson = $entry->getLessonStart(); $lesson <= $entry->getLessonEnd(); $lesson++) {
            $lessons[] = $lesson;
        }

        return $lessons;
    }
}