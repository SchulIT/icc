<?php

namespace App\StudentAbsence;

use App\Book\Student\ExcuseCollection;
use App\Entity\DateLesson;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceExcuseStatus;

class ExcuseStatusItem {

    public function __construct(private readonly DateLesson $dateLesson, private readonly ?ExcuseCollection $excuseCollection, private readonly ?LessonAttendance $attendance) {

    }

    /**
     * @return DateLesson
     */
    public function getDateLesson(): DateLesson {
        return $this->dateLesson;
    }

    /**
     * @return ?ExcuseCollection
     */
    public function getCollection(): ?ExcuseCollection {
        return $this->excuseCollection;
    }

    /**
     * @return LessonAttendance|null
     */
    public function getAttendance(): ?LessonAttendance {
        return $this->attendance;
    }

    public function isExcused(): bool {
        if($this->excuseCollection !== null && count($this->excuseCollection) > 1) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getAbsentLessons() === 0) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getExcuseStatus() === LessonAttendanceExcuseStatus::Excused) {
            return true;
        }

        return false;
    }
}