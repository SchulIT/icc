<?php

namespace App\Sorting;

use App\Grouping\ExamDateGroup;

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