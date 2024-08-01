<?php

namespace App\Sorting;

use App\Entity\Attendance;

class AttendanceStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy)
    {
    }

    /**
     * @param Attendance $objectA
     * @param Attendance $objectB
     */
    public function compare($objectA, $objectB): int {
        $dateCmp = $this->dateStrategy->compare($objectA->getEntry()->getLesson()->getDate(), $objectB->getEntry()->getLesson()->getDate());

        if($dateCmp === 0) {
            return $objectA->getLesson() - $objectB->getLesson();
        }

        return $dateCmp;
    }
}