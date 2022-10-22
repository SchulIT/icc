<?php

namespace App\Sorting;

use App\Entity\Absence;

class AbsentStudyGroupStrategy implements SortingStrategyInterface {

    public function __construct(private StudyGroupStrategy $studyGroupStrategy)
    {
    }

    /**
     * @param Absence $objectA
     * @param Absence $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->studyGroupStrategy->compare($objectA->getStudyGroup(), $objectB->getStudyGroup());
    }
}