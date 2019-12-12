<?php

namespace App\Sorting;

use App\Grouping\UserUserTypeGroup;

class UserUserTypeGroupStrategy implements SortingStrategyInterface {

    private $userTypeStrategy;

    public function __construct(UserTypeStrategy $strategy) {
        $this->userTypeStrategy = $strategy;
    }

    /**
     * @param UserUserTypeGroup $objectA
     * @param UserUserTypeGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->userTypeStrategy->compare($objectA->getUserType(), $objectB->getUserType());
    }
}