<?php

namespace App\Grouping;

use App\Date\WeekOfYear;
use DateTime;

class DateWeekOfYearStrategy implements GroupingStrategyInterface {

    /**
     * @param DateTime $object
     * @param array $options
     * @return mixed|void
     */
    public function computeKey($object, array $options = []) {
        return new WeekOfYear((int)$object->format('Y'), (int)$object->format('W'));
    }

    /**
     * @param WeekOfYear $keyA
     * @param WeekOfYear $keyB
     * @param array $options
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = []): bool {
        return $keyA->getYear() === $keyB->getYear()
            && $keyA->getWeekNumber() === $keyB->getWeekNumber();
    }

    /**
     * @param WeekOfYear $key
     * @param array $options
     * @return GroupInterface
     */
    public function createGroup($key, array $options = []): GroupInterface {
        return new DateWeekOfYearGroup($key);
    }
}