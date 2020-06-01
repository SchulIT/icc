<?php

namespace App\Sorting;

use App\Entity\Absence;

class AbsentStudyGroupStrategy implements SortingStrategyInterface {

    private $studyGroupStrategy;

    public function __construct(StudyGroupStrategy $studyGroupStrategy) {
        $this->studyGroupStrategy = $studyGroupStrategy;
    }

    /**
     * @param Absence $objectA
     * @param Absence $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->studyGroupStrategy->compare($objectA->getStudyGroup(), $objectB->getStudyGroup());
    }
}