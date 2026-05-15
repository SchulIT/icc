<?php

namespace App\Message\Grouping;

use App\Framework\Date\WeekOfYear;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;
use App\Message\Entity\Message;
use App\Message\Grouping\MessageWeekGroup;

class MessageWeekStrategy implements GroupingStrategyInterface {

    /**
     * @param Message $object
     * @return WeekOfYear|null
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
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new MessageWeekGroup($key);
    }
}