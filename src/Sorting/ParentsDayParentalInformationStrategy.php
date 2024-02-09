<?php

namespace App\Sorting;

use App\Entity\ParentsDayParentalInformation;

class ParentsDayParentalInformationStrategy implements SortingStrategyInterface {

    public function __construct(private readonly StudentStrategy $studentStrategy) {

    }

    /**
     * @param ParentsDayParentalInformation $objectA
     * @param ParentsDayParentalInformation $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->studentStrategy->compare($objectA->getStudent(), $objectB->getStudent());
    }
}