<?php

namespace App\Grouping;

use App\Entity\Message;

class MessageWeekStrategy implements GroupingStrategyInterface {

    /**
     * @param Message $object
     * @param array $options
     * @return WeekOfYear
     */
    public function computeKey($object, array $options = []) {
        if($object->getExpireDate() === null) {
            return null;
        }

        $weekNumber = (int)$object->getExpireDate()->format('W');
        $year = (int)$object->getExpireDate()->format('Y');

        return new WeekOfYear($year, $weekNumber);
    }

    /**
     * @param WeekOfYear|null $keyA
     * @param WeekOfYear|null $keyB
     * @param array $options
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
     * @param array $options
     * @return GroupInterface
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new MessageWeekGroup($key);
    }
}