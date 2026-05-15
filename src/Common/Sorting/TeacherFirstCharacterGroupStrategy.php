<?php

namespace App\Common\Sorting;

use App\Common\Grouping\TeacherFirstCharacterGroup;
use App\Framework\Sorting\SortingStrategyInterface;

class TeacherFirstCharacterGroupStrategy implements SortingStrategyInterface {

    /**
     * @param TeacherFirstCharacterGroup $objectA
     * @param TeacherFirstCharacterGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return strcmp($objectA->getFirstCharacter(), $objectB->getFirstCharacter());
    }
}