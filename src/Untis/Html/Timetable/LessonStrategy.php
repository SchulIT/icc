<?php

namespace App\Untis\Html\Timetable;

use App\Grouping\GroupingStrategyInterface;
use App\Grouping\GroupInterface;

class LessonStrategy implements GroupingStrategyInterface {

    /**
     * @param Lesson $object
     * @param array $options
     * @return string
     */
    public function computeKey($object, array $options = []) {
        return sprintf(
            '%s-%d-%d-%d-%s-%s',
            implode(',', $object->getWeeks()),
            $object->getDay(),
            $object->getLessonStart(),
            $object->getLessonEnd(),
            $object->getSubject(),
            $object->getRoom()
        );
    }

    /**
     * @param string $keyA
     * @param string $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string $key
     * @param array $options
     * @return LessonGroup
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new LessonGroup($key);
    }
}