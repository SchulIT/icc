<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\Tuition;
use App\Common\Grouping\TuitionGradeGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

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