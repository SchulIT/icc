<?php

namespace App\Book\Student;

use App\Entity\Student;

readonly class StudentStatisticsCounter {
    /*
     * @param AttendanceFlagCount[] $attendanceFlagCounts
     */
    public function __construct(
        public Student $student,
        public int $totalLessonsCount,
        public int $presentLessonCount,
        public int $absentLessonsCount,
        public int $lateMinutes,
        public int $notExcusedLessonsCount,
        public int $excuseStatusNotSetLessonsCount,
        public int $commentsCount,
        public array $attendanceFlagCounts
    ) { }
}