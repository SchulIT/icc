<?php

namespace App\Sorting;

use App\Entity\Teacher;

class TeacherStrategy implements SortingStrategyInterface {

    /**
     * @param Teacher|null $objectA
     * @param Teacher|null $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        if($objectA === null && $objectB === null) {
            return 0;
        } else if($objectA === null) {
            return -1;
        } else if($objectB === null) {
            return 1;
        }

        return strcmp($objectA->getAcronym(), $objectB->getAcronym());
    }
}