<?php

namespace App\Grouping;

use DateInterval;
use DateTime;

class WeekOfYear {

    /** @var int */
    private $year;

    /** @var int */
    private $weekNumber;

    public function __construct(int $year, int $weekNumber) {
        $this->year = $year;
        $this->weekNumber = $weekNumber;
    }

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getWeekNumber(): int {
        return $this->weekNumber;
    }

    public function getFirstDay(): DateTime {
        $dateTime = new DateTime();
        $dateTime->setDate($this->year, 1, 1);
        $diff = sprintf('P%dW', $this->weekNumber);

        return $dateTime->add(new DateInterval($diff));
    }
}