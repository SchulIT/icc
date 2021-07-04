<?php

namespace App\Grouping;

use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;

class StudyGroupTypeStrategy implements GroupingStrategyInterface {

    /**
     * @param StudyGroup $object
     * @return StudyGroupType
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getType();
    }

    /**
     * @param StudyGroupType $keyA
     * @param StudyGroupType $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->equals($keyB);
    }

    /**
     * @param StudyGroupType $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StudyGroupTypeGroup($key);
    }
}