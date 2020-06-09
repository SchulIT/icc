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
        $dateTime->setISODate($this->year, $this->weekNumber);

        return $dateTime;
    }

    public function getLastDay(): DateTime {
        $dateTime = new DateTime();
        $dateTime->setISODate($this->year, $this->weekNumber, 7);

        return $dateTime;
    }
}