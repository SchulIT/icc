<?php

namespace App\Common\Sorting;

use App\Common\Entity\Student;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Framework\Sorting\StringStrategy;

class StudentStrategy implements SortingStrategyInterface {

    public function __construct(private StringStrategy $stringStrategy)
    {
    }

    /**
     * @param Student $objectA
     * @param Student $objectB
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