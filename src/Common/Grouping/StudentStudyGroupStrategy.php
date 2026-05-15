<?php

namespace App\Common\Grouping;

use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Grouping\StudentStudyGroupGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

class StudentStudyGroupStrategy implements GroupingStrategyInterface {

    /**
     * @param Student $object
     * @return StudyGroup[]
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getStudyGroupMemberships()
            ->map(fn(StudyGroupMembership $membership) => $membership->getStudyGroup())
            ->toArray();
    }

    /**
     * @param StudyGroup $keyA
     * @param StudyGroup $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param StudyGroup $key
     * @return StudentStudyGroupGroup
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StudentStudyGroupGroup($key);
    }
}