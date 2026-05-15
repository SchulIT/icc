<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\Tuition;
use App\Common\Grouping\StudyGroupGradeGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

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