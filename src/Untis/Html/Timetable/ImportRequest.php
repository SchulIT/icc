<?php

namespace App\Untis\Html\Timetable;

use DateTime;

readonly class ImportRequest {

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @param ImportRequestTimetable[] $timetables
     */
    public function __construct(public DateTime $start, public DateTime $end, public array $timetables) {

    }
}