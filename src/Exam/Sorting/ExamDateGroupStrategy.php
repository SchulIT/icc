<?php

namespace App\Exam\Sorting;

use App\Framework\Sorting\DateStrategy;
use App\Exam\Grouping\ExamDateGroup;
use App\Framework\Sorting\SortingStrategyInterface;

class ExamDateGroupStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStratgy)
    {
    }

    /**
     * @param ExamDateGroup $objectA
     * @param ExamDateGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStratgy->compare($objectA->getDate(), $objectB->getDate());
    }
}