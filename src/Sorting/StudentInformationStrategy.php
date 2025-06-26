<?php

namespace App\Sorting;

use App\Entity\StudentInformation;

class StudentInformationStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) {

    }

    /**
     * @param StudentInformation $objectA
     * @param StudentInformation $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectB->getFrom(), $objectA->getFrom());
    }
}