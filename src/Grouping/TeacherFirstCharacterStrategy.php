<?php

namespace App\Grouping;

use App\Entity\Teacher;

class TeacherFirstCharacterStrategy implements GroupingStrategyInterface {

    /**
     * @param Teacher $object
     * @return string
     */
    public function computeKey($object, array $options = [ ]) {
        return ucfirst(substr($object->getAcronym(), 0, 1));
    }

    /**
     * @param string $keyA
     * @param string $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new TeacherFirstCharacterGroup($key);
    }
}