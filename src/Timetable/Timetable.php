<?php

namespace App\Timetable;

class Timetable {
    /**
     * @var TimetableWeek[]
     */
    private array $weeks = [ ];

    /**
     * @return TimetableWeek[]
     */
    public function &getWeeks() {
        return $this->weeks;
    }

    public function addWeek(TimetableWeek $week): void {
        $this->weeks[] = $week;
    }

    /**
     * @param TimetableWeek[] $weeks
     */
    public function setWeeks(array $weeks): void {
        $this->weeks = $weeks;
    }
}