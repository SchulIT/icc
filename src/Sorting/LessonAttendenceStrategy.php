<?php

namespace App\Sorting;

use App\Entity\Attendance;

class LessonAttendenceStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param Attendance $objectA
     * @param Attendance $objectB
     */
    public function compare($objectA, $objectB): int {
        $dateCmp = $this->dateStrategy->compare($objectA->getEntry()->getLesson()->getDate(), $objectB->getEntry()->getLesson()->getDate());

        if($dateCmp === 0) {
            return $objectA->getEntry()->getLessonStart() - $objectB->getEntry()->getLessonStart();
        }

        return $dateCmp;
    }
}