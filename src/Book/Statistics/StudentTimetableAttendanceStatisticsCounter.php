<?php

namespace App\Book\Statistics;

use DateTime;

readonly class StudentTimetableAttendanceStatisticsCounter {
    public function __construct(
        public int $weekDay,
        public int $lessonNumber,
        public int $lateMinutes,
        public int $totalLate,
        public int $totalAbsences,
        public int $totalAttendances
    ) { }
}