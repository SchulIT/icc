<?php

namespace App\Sorting;

use App\Grouping\StudentStudyGroupGroup;

class StudentStudyGroupGroupStrategy implements SortingStrategyInterface {

    public function __construct(private StudyGroupStrategy $studyGroupStrategy)
    {
    }

    /**
     * @param StudentStudyGroupGroup $objectA
     * @param StudentStudyGroupGroup $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->studyGroupStrategy->compare($objectA->getStudyGroup(), $objectB->getStudyGroup());
    }
}