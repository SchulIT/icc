<?php

namespace App\Untis\Database\Date;

use DateTime;

class Date {

    private ?int $calendarWeek = null;

    private ?\DateTime $startDate = null;

    private ?int $schoolWeek = null;

    public function getCalendarWeek(): int {
        return $this->calendarWeek;
    }

    public function setCalendarWeek(int $calendarWeek): Date {
        $this->calendarWeek = $calendarWeek;
        return $this;
    }

    public function getStartDate(): DateTime {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): Date {
        $this->startDate = $startDate;
        return $this;
    }

    public function getSchoolWeek(): int {
        return $this->schoolWeek;
    }

    public function setSchoolWeek(int $schoolWeek): Date {
        $this->schoolWeek = $schoolWeek;
        return $this;
    }
}