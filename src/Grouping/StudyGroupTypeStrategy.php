<?php

namespace App\Grouping;

use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;

class StudyGroupTypeStrategy implements GroupingStrategyInterface {

    /**
     * @param StudyGroup $object
     * @return StudyGroupType
     */
    public function computeKey($object) {
        return $object->getType();
    }

    /**
     * @param StudyGroupType $keyA
     * @param StudyGroupType $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param StudyGroupType $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new StudyGroupTypeGroup($key);
    }
}