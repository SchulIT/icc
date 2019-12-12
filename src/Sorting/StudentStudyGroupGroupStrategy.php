<?php

namespace App\Sorting;

use App\Grouping\StudentStudyGroupGroup;

class StudentStudyGroupGroupStrategy implements SortingStrategyInterface {

    private $studyGroupStrategy;

    public function __construct(StudyGroupStrategy $studyGroupStrategy) {
        $this->studyGroupStrategy = $studyGroupStrategy;
    }

    /**
     * @param StudentStudyGroupGroup $objectA
     * @param StudentStudyGroupGroup $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->studyGroupStrategy->compare($objectA->getStudyGroup(), $objectB->getStudyGroup());
    }
}