<?php

namespace App\Sorting;

use App\Entity\User;

class UserLastnameFirstnameStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param User $objectA
     * @param User $objectB
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