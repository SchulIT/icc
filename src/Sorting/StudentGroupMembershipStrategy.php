<?php

namespace App\Sorting;

use App\Entity\StudyGroupMembership;

class StudentGroupMembershipStrategy implements SortingStrategyInterface {

    private $studentStrategy;

    public function __construct(StudentStrategy $studentStrategy) {
        $this->studentStrategy = $studentStrategy;
    }

    /**
     * @param StudyGroupMembership $objectA
     * @param StudyGroupMembership $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->studentStrategy->compare($objectA->getStudent(), $objectB->getStudent());
    }
}