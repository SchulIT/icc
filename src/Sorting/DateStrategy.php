<?php

namespace App\Sorting;

use DateTimeInterface;
class DateStrategy implements SortingStrategyInterface {

    /**
     * @param DateTimeInterface $objectA
     * @param DateTimeInterface $objectB
     */
    public function compare($objectA, $objectB): int
    {
        return $objectA <=> $objectB;
    }
}