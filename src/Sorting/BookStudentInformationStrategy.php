<?php

namespace App\Sorting;

use App\Entity\BookStudentInformation;

class BookStudentInformationStrategy implements SortingStrategyInterface {

    public function __construct(private readonly DateStrategy $dateStrategy) {

    }

    /**
     * @param BookStudentInformation $objectA
     * @param BookStudentInformation $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->dateStrategy->compare($objectB->getFrom(), $objectA->getFrom());
    }
}