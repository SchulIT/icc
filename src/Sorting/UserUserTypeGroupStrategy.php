<?php

namespace App\Sorting;

use App\Grouping\UserUserTypeGroup;

class UserUserTypeGroupStrategy implements SortingStrategyInterface {

    public function __construct(private UserTypeStrategy $userTypeStrategy)
    {
    }

    /**
     * @param UserUserTypeGroup $objectA
     * @param UserUserTypeGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->userTypeStrategy->compare($objectA->getUserType(), $objectB->getUserType());
    }
}