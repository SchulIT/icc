<?php

namespace App\Common\Sorting;

use App\Common\Grouping\UserUserTypeGroup;
use App\Common\Sorting\UserTypeStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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