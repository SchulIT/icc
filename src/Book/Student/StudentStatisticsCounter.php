<?php

namespace App\Book\Student;

use App\Entity\AttendanceFlag;
use App\Entity\Student;

readonly class StudentStatisticsCounter {
    /**
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

    public function getAttendanceFlagCount(AttendanceFlag $flag): int {
        foreach($this->attendanceFlagCounts as $attendanceFlagCount) {
            if($attendanceFlagCount->getFlag() === $flag) {
                return $attendanceFlagCount->getCount();
            }
        }

        return 0;
    }
}