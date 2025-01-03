<?php

namespace App\Sorting;

use App\Entity\TeacherAbsenceComment;

class TeacherAbsenceCommentStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) { }

    /**
     * @param TeacherAbsenceComment $objectA
     * @param TeacherAbsenceComment $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $compareDate = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($compareDate === 0) {
            return $objectA->getLessonStart() - $objectB->getLessonStart();
        }

        return $compareDate;
    }
}