<?php

namespace App\Sorting;

use DateTimeInterface;
class DateStrategy implements SortingStrategyInterface {

    /**
     * @param DateTimeInterface|null $objectA
     * @param DateTimeInterface|null $objectB
     */
    public function compare($objectA, $objectB): int
    {
        return $objectA <=> $objectB;
    }
}