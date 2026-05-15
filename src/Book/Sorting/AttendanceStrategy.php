<?php

namespace App\Book\Sorting;

use App\Book\Entity\Attendance;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

class AttendanceStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy)
    {
    }

    /**
     * @param Attendance $objectA
     * @param Attendance $objectB
     */
    public function compare($objectA, $objectB): int {
        $dateCmp = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($dateCmp === 0) {
            return $objectA->getLesson() - $objectB->getLesson();
        }

        return $dateCmp;
    }
}