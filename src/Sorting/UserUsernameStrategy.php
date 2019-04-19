<?php

namespace App\Sorting;

use App\Entity\User;

class UserUsernameStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $strategy) {
        $this->stringStrategy = $strategy;
    }

    /**
     * @param User $objectA
     * @param User $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->stringStrategy->compare($objectA->getUsername(), $objectB->getUsername());
    }
}