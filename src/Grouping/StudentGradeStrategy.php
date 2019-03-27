<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Student;

class StudentGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param Student $object
     * @return Grade
     */
    public function computeKey($object) {
        return $object->getGrade();
    }

    /**
     * @param Grade $keyA
     * @param Grade $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->getName() === $keyB->getName();
    }

    /**
     * @param Grade $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new StudentGradeGroup($key);
    }
}