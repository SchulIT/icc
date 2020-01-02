<?php

namespace App\Sorting;


use Gedmo\Loggable\Entity\LogEntry;

class LogEntryStrategy implements SortingStrategyInterface {

    private $dateStrategy;

    public function __construct(DateStrategy $dateStrategy) {
        $this->dateStrategy = $dateStrategy;
    }

    /**
     * @param LogEntry $objectA
     * @param LogEntry $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectA->getLoggedAt(), $objectB->getLoggedAt());
    }
}