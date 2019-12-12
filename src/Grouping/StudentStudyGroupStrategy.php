<?php

namespace App\Grouping;

use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;

class StudentStudyGroupStrategy implements GroupingStrategyInterface {

    /**
     * @param Student $object
     * @return StudyGroup[]
     */
    public function computeKey($object) {
        return $object->getStudyGroupMemberships()
            ->map(function(StudyGroupMembership $membership) {
                return $membership->getStudyGroup();
            })
            ->toArray();
    }

    /**
     * @param StudyGroup $keyA
     * @param StudyGroup $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param StudyGroup $key
     * @return StudentStudyGroupGroup
     */
    public function createGroup($key): GroupInterface {
        return new StudentStudyGroupGroup($key);
    }
}