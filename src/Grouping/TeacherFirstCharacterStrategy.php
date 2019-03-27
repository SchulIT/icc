<?php

namespace App\Grouping;

use App\Entity\Teacher;

class TeacherFirstCharacterStrategy implements GroupingStrategyInterface {

    /**
     * @param Teacher $object
     * @return string
     */
    public function computeKey($object) {
        return ucfirst($object->getAcronym());
    }

    /**
     * @param string $keyA
     * @param string $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new TeacherFirstCharacterGroup($key);
    }
}