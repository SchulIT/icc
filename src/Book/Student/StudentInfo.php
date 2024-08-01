<?php

namespace App\Book\Student;

use App\Entity\BookComment;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceFlag;
use App\Entity\AttendanceType;
use App\Entity\Student;
use App\Entity\Attendance as LessonAttendanceEntity;
use DateTime;

class StudentInfo {

    /**
     * @param LessonAttendance[] $lateLessonAttendances
     * @param LessonAttendance[] $absentLessonAttendances
     * @param LessonAttendance[] $presentLessonAttendances
     * @param BookComment[] $comments
     * @param AttendanceFlagCount[] $attendanceFlagCounts
     */
    public function __construct(private readonly Student $student,
                                private readonly int $totalLessonsCount,
                                private readonly array $lateLessonAttendances,
                                private readonly array $absentLessonAttendances,
                                private readonly array $presentLessonAttendances,
                                private readonly array $comments,
                                private readonly array $attendanceFlagCounts)
    {
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function getTotalLessonsCount(): int {
        return $this->totalLessonsCount;
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
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getLateMinutes(),
                $this->getLateLessonAttendances()
            )
        );
    }

    public function getAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getType() === AttendanceType::Absent
                    && $attendance->getAttendance()->isZeroAbsentLesson() === false ? 1 : 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedOrNotSetLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getExcuseStatus() === AttendanceExcuseStatus::NotSet
                    && $attendance->getExcuses()->count() === 0
                    && $attendance->getAttendance()->isZeroAbsentLesson() === false ? 1 : 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getExcuseStatus() === AttendanceExcuseStatus::NotExcused
                    && $attendance->getExcuses()->count() === 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getExcuseCollectionForLesson(LessonAttendanceEntity $attendance): ExcuseCollection {
        foreach($this->absentLessonAttendances as $absentLessonAttendance) {
            if($absentLessonAttendance->getAttendance() === $attendance) {
                return $absentLessonAttendance->getExcuses();
            }
        }

        return new ExcuseCollection(clone $attendance->getEntry()->getLesson()->getDate(), $attendance->getEntry()->getLessonStart());
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
        return $this->attendanceFlagCounts;
    }

    public function getAttendanceFlagCount(AttendanceFlag $flag): ?AttendanceFlagCount {
        foreach($this->attendanceFlagCounts as $attendanceFlagCount) {
            if($attendanceFlagCount->getFlag()->getId() === $flag->getId()) {
                return $attendanceFlagCount;
            }
        }

        return null;
    }
}