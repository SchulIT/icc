<?php

namespace App\Timetable;

class Timetable {
    /**
     * @var TimetableWeek[]
     */
    private $weeks = [ ];

    /**
     * @return TimetableWeek[]
     */
    public function getWeeks() {
        return $this->weeks;
    }

    public function addWeek(TimetableWeek $week): void {
        $this->weeks[] = $week;
    }
}