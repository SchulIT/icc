<?php

namespace App\Framework\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use DateTimeInterface;

/**
 * @implements SortingStrategyInterface<DateTimeInterface>
 */
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