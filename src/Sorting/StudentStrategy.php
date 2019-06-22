<?php

namespace App\Sorting;

use App\Entity\Student;

class StudentStrategy implements SortingStrategyInterface {

    private $stringStrategy;

    public function __construct(StringStrategy $stringStrategy) {
        $this->stringStrategy = $stringStrategy;
    }

    /**
     * @param Student $objectA
     * @param Student $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $compareLastname = $this->stringStrategy->compare($objectA->getLastname(), $objectB->getLastname());
        $compareFirstname = $this->stringStrategy->compare($objectA->getFirstname(), $objectB->getFirstname());

        if($compareLastname === 0) {
            return $compareFirstname;
        }

        return $compareLastname;
    }
}