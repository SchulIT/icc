<?php

namespace App\Book\Statistics;

use DateTime;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class GenerateStudentTimetableAttendanceStatisticsMessage {
    public function __construct(
        public int $studentId,
        public DateTime $start,
        public DateTime $end,
    ) { }
}