<?php

namespace App\Substitution\Sorting;

use App\Framework\Sorting\SortingStrategyInterface;
use App\Common\Sorting\StudyGroupStrategy;
use App\Substitution\Entity\Absence;

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