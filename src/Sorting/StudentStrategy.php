<?php

namespace App\Sorting;

use App\Entity\Student;

class StudentStrategy implements SortingStrategyInterface {

    /**
     * @param Student $objectA
     * @param Student $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        $compareLastname = strcmp($objectA->getLastname(), $objectB->getLastname());
        $compareFirstname = strcmp($objectA->getFirstname(), $objectB->getFirstname());

        if($compareLastname === 0) {
            return $compareFirstname;
        }

        return $compareLastname;
    }
}