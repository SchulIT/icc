<?php

namespace App\Grouping;

use App\Entity\Exam;

class ExamWeekStrategy implements GroupingStrategyInterface {

    /**
     * @param Exam $object
     * @return WeekOfYear|null
     */
    public function computeKey($object, array $options = [ ]) {
        if($object->getDate() === null) {
            return null;
        }

        $weekNumber = (int)$object->getDate()->format('W');
        $year = (int)$object->getDate()->format('Y');

        return new WeekOfYear($year, $weekNumber);
    }

    /**
     * @param WeekOfYear|null $keyA
     * @param WeekOfYear|null $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        if($keyA === null && $keyB === null) {
            return true;
        } else if($keyA === null || $keyB === null) {
            return false;
        }

        return $keyA->getWeekNumber() === $keyB->getWeekNumber()
            && $keyA->getYear() === $keyB->getYear();
    }

    /**
     * @param WeekOfYear $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new ExamWeekGroup($key);
    }
}