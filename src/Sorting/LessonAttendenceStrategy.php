<?php

namespace App\Sorting;

use App\Entity\LessonAttendance;

class LessonAttendenceStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param LessonAttendance $objectA
     * @param LessonAttendance $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $dateCmp = $this->dateStrategy->compare($objectA->getEntry()->getDate(), $objectB->getEntry()->getDate());

        if($dateCmp === 0) {
            return $objectA->getEntry()->getLessonStart() - $objectB->getEntry()->getLessonStart();
        }

        return $dateCmp;
    }
}