<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Tuition;

class StudyGroupGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param Tuition $object
     * @return Grade[]
     */
    public function computeKey($object) {
        return $object->getStudyGroup()->getGrades()->toArray();
    }

    /**
     * @param Grade $keyA
     * @param Grade $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new StudyGroupGradeGroup($key);
    }
}