<?php

namespace App\Book\Student;

use App\Entity\BookComment;
use App\Entity\LessonAttendanceExcuseStatus;
use App\Entity\LessonAttendanceType;
use App\Entity\Student;
use App\Entity\LessonAttendance as LessonAttendanceEntity;
use DateTime;

class StudentInfo {

    /**
     * @param LessonAttendance[] $lateLessonAttendances
     * @param LessonAttendance[] $absentLessonAttendances
     * @param LessonAttendance[] $presentLessonAttendances
     * @param BookComment[] $comments
     */
    public function __construct(private readonly Student $student, private readonly int $totalLessonsCount, private readonly array $lateLessonAttendances, private readonly array $absentLessonAttendances, private readonly array $presentLessonAttendances, private readonly array $comments)
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
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getType() === LessonAttendanceType::Absent
                    && $attendance->getAttendance()->getAbsentLessons() > 0
                    && ($attendance->getAttendance()->getEntry()->getLessonEnd() - $attendance->getAttendance()->getAbsentLessons()) < $attendance->getLesson() ? 1 : 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedOrNotSetLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::NotSet
                    && $attendance->getExcuses()->count() === 0
                    && $attendance->getAttendance()->getAbsentLessons() > 0
                    && ($attendance->getAttendance()->getEntry()->getLessonEnd() - $attendance->getAttendance()->getAbsentLessons()) < $attendance->getLesson() ? 1 : 0,
                $this->getAbsentLessonAttendances()
            )
        );
    }

    public function getNotExcusedAbsentLessonsCount(): int {
        return array_sum(
            array_map(
                fn(LessonAttendance $attendance) => $attendance->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::NotExcused
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
}