<?php

namespace App\Book\Student;

use App\Entity\AttendanceFlag;
use App\Entity\BookComment;
use App\Entity\Student;
use DateTime;

readonly class StudentInfo {

    /**
     * @param LessonAttendance[] $lateLessonAttendances
     * @param LessonAttendance[] $absentLessonAttendances
     * @param LessonAttendance[] $presentLessonAttendances
     * @param BookComment[] $comments
     */
    public function __construct(private Student $student,
                                private StudentStatisticsCounter $counter,
                                private array $lateLessonAttendances,
                                private array $absentLessonAttendances,
                                private array $presentLessonAttendances,
                                private array $comments)
    {
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getTotalLessonsCount(): int {
        return $this->counter->totalLessonsCount;
    }

    /**
     * @return LessonAttendance[]
     */
    public function getPresentLessonAttendances(): array {
        return $this->presentLessonAttendances;
    }

    /**
     * @return LessonAttendance[]
     */
    public function getLateLessonAttendances(): array {
        return $this->lateLessonAttendances;
    }

    /**
     * @return LessonAttendance[]
     */
    public function getAbsentLessonAttendances(): array {
        return $this->absentLessonAttendances;
    }

    public function getLateMinutesCount(): int {
        return $this->counter->lateMinutes;
    }

    public function getAbsentLessonsCount(): int {
        return $this->counter->absentLessonsCount;
    }

    public function getExcuseStatusNotSetLessonsCount(): int {
        return $this->counter->excuseStatusNotSetLessonsCount;
    }

    public function getNotExcusedAbsentLessonsCount(): int {
        return $this->counter->notExcusedLessonsCount;
    }

    public function getAbsentAttendance(DateTime $dateTime, int $lessonNumber): ?LessonAttendance {
        foreach($this->absentLessonAttendances as $attendance) {
            if($attendance->getDate() == $dateTime && $attendance->getLesson() === $lessonNumber) {
                return $attendance;
            }
        }

        return null;
    }

    public function getLateAttendance(DateTime $dateTime, int $lessonNumber): ?LessonAttendance {
        foreach($this->lateLessonAttendances as $attendance) {
            if($attendance->getDate() == $dateTime && $attendance->getLesson() === $lessonNumber) {
                return $attendance;
            }
        }

        return null;
    }

    /**
     * @return BookComment[]
     */
    public function getComments(): array {
        return $this->comments;
    }

    /**
     * @return AttendanceFlagCount[]
     */
    public function getAttendanceFlagCounts(): array {
        return $this->counter->attendanceFlagCounts;
    }

    public function getAttendanceFlagCount(AttendanceFlag $flag): ?AttendanceFlagCount {
        foreach($this->counter->attendanceFlagCounts as $attendanceFlagCount) {
            if($attendanceFlagCount->getFlag()->getId() === $flag->getId()) {
                return $attendanceFlagCount;
            }
        }

        return null;
    }
}