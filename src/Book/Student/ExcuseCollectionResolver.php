<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Settings\TimetableSettings;

class ExcuseCollectionResolver {

    public function __construct(private readonly TimetableSettings $timetableSettings) {

    }

    /**
     * @param ExcuseNote[] $excuseNotes
     * @return ExcuseCollection[]
     */
    public function resolve(array $excuseNotes): array {
        /** @var ExcuseCollection[] $collection */
        $collection = [ ];

        foreach($excuseNotes as $excuseNote) {
            for($date = clone $excuseNote->getFrom()->getDate(); $date <= $excuseNote->getUntil()->getDate(); $date->modify('+1 day')) {
                for($lesson = ($date == $excuseNote->getFrom()->getDate() ? $excuseNote->getFrom()->getLesson() : 1);
                    $lesson <= ($date == $excuseNote->getUntil()->getDate() ? $excuseNote->getUntil()->getLesson() : $this->timetableSettings->getMaxLessons());
                    $lesson++
                ) {
                    $key = sprintf('%s-%d', $date->format('Y-m-d'), $lesson);

                    if(!isset($collection[$key])) {
                        $collection[$key] = new ExcuseCollection(clone $date, $lesson);
                    }

                    $collection[$key]->add($excuseNote);
                }
            }
        }

        return $collection;
    }
}