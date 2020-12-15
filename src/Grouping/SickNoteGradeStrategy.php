<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\SickNote;

class SickNoteGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param SickNote $object
     * @return Grade
     */
    public function computeKey($object) {
        return $object->getStudent()->getGrade();
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
        return new SickNoteGradeGroup($key);
    }
}