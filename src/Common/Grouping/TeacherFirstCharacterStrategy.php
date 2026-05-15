<?php

namespace App\Common\Grouping;

use App\Common\Entity\Teacher;
use App\Common\Grouping\TeacherFirstCharacterGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

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
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new TeacherFirstCharacterGroup($key);
    }
}