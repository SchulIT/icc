<?php

namespace App\Book\Statistics;

use DateTime;
use InvalidArgumentException;

readonly class StudentTimetableAttendanceStatistics {

    /**
     * @param StudentTimetableAttendanceStatisticsCounter[] $counter
     */
    public function __construct(
        public int $studentId,
        public DateTime $start,
        public DateTime $end,
        public array $counter,
        public DateTime $generatedAt
    ) {  }

    public function getCounter(int $weekDay, int $lessonNumber): StudentTimetableAttendanceStatisticsCounter {
        foreach($this->counter as $timetableAttendance) {
            if($timetableAttendance->weekDay === $weekDay && $timetableAttendance->lessonNumber === $lessonNumber) {
                return $timetableAttendance;
            }
        }

        throw new InvalidArgumentException(sprintf('StudentTimetableAttendanceStatisticsCounter für Tag %d und Stunde %d nicht vorhanden.', $weekDay, $lessonNumber));
    }
}