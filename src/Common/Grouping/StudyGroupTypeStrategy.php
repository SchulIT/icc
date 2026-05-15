<?php

namespace App\Common\Grouping;

use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupType;
use App\Common\Grouping\StudyGroupTypeGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

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
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param StudyGroupType $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StudyGroupTypeGroup($key);
    }
}