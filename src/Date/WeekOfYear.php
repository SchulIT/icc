<?php

namespace App\Date;

use DateInterval;
use DateTime;

class WeekOfYear {

    public function __construct(private int $year, private int $weekNumber)
    {
    }

    public function getYear(): int {
        return $this->year;
    }

    public function getWeekNumber(): int {
        return $this->weekNumber;
    }

    public function getFirstDay(): DateTime {
        $dateTime = new DateTime();
        $dateTime->setTime(0,0,0);
        $dateTime->setISODate($this->year, $this->weekNumber);

        return $dateTime;
    }

    public function getLastDay(): DateTime {
        $dateTime = new DateTime();
        $dateTime->setTime(0,0,0);
        $dateTime->setISODate($this->year, $this->weekNumber, 7);

        return $dateTime;
    }
}