<?php

namespace App\ParentsDay\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\ParentsDay\Entity\ParentsDayParentalInformation;
use App\Common\Sorting\StudentStrategy;

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