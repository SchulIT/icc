<?php

namespace App\Sorting;

use App\Entity\UserType;

class UserTypeStrategy implements SortingStrategyInterface {

    /**
     * @param UserType $objectA
     * @param UserType $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getValue(), $objectB->getValue());
    }
}