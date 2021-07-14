<?php

namespace App\Validator;

use App\Entity\LessonEntry;
use App\Repository\LessonEntryRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueLessonEntryValidator extends ConstraintValidator {

    private $entryRepository;

    public function __construct(LessonEntryRepositoryInterface $entryRepository) {
        $this->entryRepository = $entryRepository;
    }

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

        if($value->getTuition() === null || $value->getDate() === null) {
            return;
        }

        $entries = $this->entryRepository->findAllByTuition($value->getTuition(), $value->getDate(), $value->getDate());
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
     * @param LessonEntry $entry
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