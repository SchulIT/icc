<?php

namespace App\Sorting;

use App\Entity\TeacherAbsenceLesson;

class TeacherAbsenceLessonStrategy implements SortingStrategyInterface {

    public function __construct(private readonly TimetableLessonStrategy $lessonStrategy) { }

    /**
     * @param TeacherAbsenceLesson $objectA
     * @param TeacherAbsenceLesson $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA->getLesson() === null && $objectB->getLesson() === null) {
            return 0;
        }

        if($objectA->getLesson() === null) {
            return -1;
        }

        if($objectB->getLesson() === null) {
            return 1;
        }

        return $this->lessonStrategy->compare($objectA->getLesson(), $objectB->getLesson());
    }
}