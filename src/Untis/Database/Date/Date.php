<?php

namespace App\Untis\Database\Date;

use DateTime;

class Date {

    /**
     * @var int
     */
    private $calendarWeek;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var int
     */
    private $schoolWeek;

    /**
     * @return int
     */
    public function getCalendarWeek(): int {
        return $this->calendarWeek;
    }

    /**
     * @param int $calendarWeek
     * @return Date
     */
    public function setCalendarWeek(int $calendarWeek): Date {
        $this->calendarWeek = $calendarWeek;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     * @return Date
     */
    public function setStartDate(DateTime $startDate): Date {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getSchoolWeek(): int {
        return $this->schoolWeek;
    }

    /**
     * @param int $schoolWeek
     * @return Date
     */
    public function setSchoolWeek(int $schoolWeek): Date {
        $this->schoolWeek = $schoolWeek;
        return $this;
    }
}