<?php

namespace App\Book\Sorting;

use App\Book\IntegrityCheck\IntegrityCheckViolation;
use App\Framework\Sorting\DateStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

class IntegrityCheckViolationStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) { }

    /**
     * @param IntegrityCheckViolation $objectA
     * @param IntegrityCheckViolation $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $cmp = $this->dateStrategy->compare($objectA->getDate(), $objectB->getDate());

        if($cmp === 0) {
            return $objectA->getLesson() - $objectB->getLesson();
        }

        return $cmp;
    }
}