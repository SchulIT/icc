<?php

namespace App\Sorting;

class DateStrategy implements SortingStrategyInterface {

    /**
     * @param \DateTimeInterface $objectA
     * @param \DateTimeInterface $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA === $objectB) {
            return 0;
        }

        return $objectA < $objectB ? -1 : 1;
    }
}