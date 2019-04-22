<?php

namespace App\Sorting;

use App\Grouping\ExamDateGroup;

class ExamDateGroupStrategy implements SortingStrategyInterface {

    private $dateStratgy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStratgy = $dateStrategy;
    }

    /**
     * @param ExamDateGroup $objectA
     * @param ExamDateGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStratgy->compare($objectA->getDate(), $objectB->getDate());
    }
}