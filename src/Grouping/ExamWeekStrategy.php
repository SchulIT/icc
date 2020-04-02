<?php

namespace App\Grouping;

use App\Entity\Exam;

class ExamWeekStrategy implements GroupingStrategyInterface {

    /**
     * @param Exam $object
     * @return int
     */
    public function computeKey($object) {
        if($object->getDate() === null) {
            return null;
        }

        return (int)$object->getDate()->format('W');
    }

    /**
     * @param int|null $keyA
     * @param int|null $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param int $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new ExamWeekGroup($key);
    }
}