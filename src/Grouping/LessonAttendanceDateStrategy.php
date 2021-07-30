<?php

namespace App\Grouping;

use App\Book\Student\LessonAttendance;
use DateTime;

class LessonAttendanceDateStrategy implements GroupingStrategyInterface {

    /**
     * @param LessonAttendance $object
     * @return DateTime
     */
    public function computeKey($object, array $options = [ ]) {
        return $object->getDate();
    }

    /**
     * @param DateTime $keyA
     * @param DateTime $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA == $keyB;
    }

    /**
     * @param DateTime $key
     * @return LessonAttendanceCommentsGroup
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new LessonAttendanceCommentsGroup($key);
    }
}