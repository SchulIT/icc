<?php

namespace App\Sorting;

use App\Entity\User;

class UserLastnameFirstnameStrategy implements SortingStrategyInterface {

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
        // Sort by lastname first
        if(0 !== $cmpLastname = $this->stringStrategy->compare($objectA->getLastname(), $objectB->getLastname())) {
            return $cmpLastname;
        }

        // Lastnames are equal -> sort by firstname
        return $this->stringStrategy->compare($objectA->getFirstname(), $objectB->getFirstname());
    }
}