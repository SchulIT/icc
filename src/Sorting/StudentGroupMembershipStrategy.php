<?php

namespace App\Sorting;

use App\Entity\StudyGroupMembership;

class StudentGroupMembershipStrategy implements SortingStrategyInterface {

    public function __construct(private StudentStrategy $studentStrategy)
    {
    }

    /**
     * @param StudyGroupMembership $objectA
     * @param StudyGroupMembership $objectB
     */
    public function compare($objectA, $objectB): int {
        return $this->studentStrategy->compare($objectA->getStudent(), $objectB->getStudent());
    }
}