<?php

namespace App\Book\Grouping;

use App\Book\Grouping\LessonDayGroup;
use App\Book\Lesson;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;
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