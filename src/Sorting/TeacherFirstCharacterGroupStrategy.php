<?php

namespace App\Sorting;

use App\Grouping\TeacherFirstCharacterGroup;

class TeacherFirstCharacterGroupStrategy implements SortingStrategyInterface {

    /**
     * @param TeacherFirstCharacterGroup $objectA
     * @param TeacherFirstCharacterGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getFirstCharacter(), $objectB->getFirstCharacter());
    }
}