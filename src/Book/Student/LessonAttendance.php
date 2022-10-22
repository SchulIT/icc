<?php

namespace App\Book\Student;

use App\Entity\LessonAttendance as LessonAttendanceEntity;
use App\Entity\LessonAttendanceExcuseStatus;
use DateTime;

class LessonAttendance {

    public function __construct(private DateTime $date, private int $lesson, private LessonAttendanceEntity $attendance, private ExcuseCollection $excuses)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function getAttendance(): LessonAttendanceEntity {
        return $this->attendance;
    }

    public function isExcused(): bool {
        if($this->getExcuses()->count() > 0) {
            return true;
        }

        if($this->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::NotExcused) {
            return false;
        }

        if($this->getAttendance()->getExcuseStatus() === LessonAttendanceExcuseStatus::Excused) {
            return true;
        }

        return false;
    }

    public function getExcuses(): ExcuseCollection {
        return $this->excuses;
    }
}