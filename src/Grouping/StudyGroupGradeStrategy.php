<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\Tuition;

class StudyGroupGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param StudyGroup $object
     * @return Grade[]
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getGrades()->toArray();
    }

    /**
     * @param Grade $keyA
     * @param Grade $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new StudyGroupGradeGroup($key);
    }
}