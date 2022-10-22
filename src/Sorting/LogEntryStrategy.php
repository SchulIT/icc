<?php

namespace App\Sorting;


use Gedmo\Loggable\Entity\LogEntry;

class LogEntryStrategy implements SortingStrategyInterface {

    public function __construct(private DateStrategy $dateStrategy)
    {
    }

    /**
     * @param LogEntry $objectA
     * @param LogEntry $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getLoggedAt(), $objectB->getLoggedAt());
    }
}