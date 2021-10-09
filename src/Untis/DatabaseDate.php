<?php

namespace App\Untis;

use DateTime;

class DatabaseDate {

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
     * @return DatabaseDate
     */
    public function setCalendarWeek(int $calendarWeek): DatabaseDate {
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
     * @return DatabaseDate
     */
    public function setStartDate(DateTime $startDate): DatabaseDate {
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
     * @return DatabaseDate
     */
    public function setSchoolWeek(int $schoolWeek): DatabaseDate {
        $this->schoolWeek = $schoolWeek;
        return $this;
    }
}