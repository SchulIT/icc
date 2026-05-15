<?php

namespace App\Common\Sorting;

use App\Common\Grouping\StudentStudyGroupGroup;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Common\Sorting\StudyGroupStrategy;

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