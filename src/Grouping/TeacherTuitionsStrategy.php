<?php

namespace App\Grouping;

use App\Entity\Teacher;
use App\Entity\Tuition;

class TeacherTuitionsStrategy implements GroupingStrategyInterface {

    /**
     * @param Tuition $object
     * @param array $options
     * @return Teacher[]
     */
    public function computeKey($object, array $options = []) {
        return $object->getTeachers()->toArray();
    }

    /**
     * @param Teacher $keyA
     * @param Teacher $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Teacher $key
     * @param array $options
     * @return GroupInterface
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new TeacherTuitionsGroup($key);
    }
}