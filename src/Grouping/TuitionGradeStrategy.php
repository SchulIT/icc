<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Tuition;

class TuitionGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param Tuition $object
     * @param array $options
     * @return Grade[]
     */
    public function computeKey($object, array $options = []) {
        $grades = $object->getStudyGroup()->getGrades()->toArray();

        return $grades;
    }

    /**
     * @param Grade $keyA
     * @param Grade $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade $key
     * @param array $options
     * @return GroupInterface
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new TuitionGradeGroup($key);
    }
}