<?php

namespace App\Common\Sorting;

use App\Common\Entity\StudyGroupMembership;
use App\Common\Sorting\StudentStrategy;
use App\Framework\Sorting\SortingStrategyInterface;

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