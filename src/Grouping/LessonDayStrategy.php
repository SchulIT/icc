<?php

namespace App\Grouping;

use App\Book\Lesson;
use DateTime;

class LessonDayStrategy implements GroupingStrategyInterface {

    /**
     * @param DateTime $keyA
     * @param DateTime $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA == $keyB;
    }

    /**
     * @param Lesson $object
     * @return DateTime
     */
    public function computeKey($object, array $options = []) {
        return $object->getDate();
    }

    /**
     * @param DateTime $key
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new LessonDayGroup($key);
    }
}