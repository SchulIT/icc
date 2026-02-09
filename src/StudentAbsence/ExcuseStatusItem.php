<?php

namespace App\StudentAbsence;

use App\Entity\DateLesson;
use App\Entity\Attendance;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\StudentAbsence;
use App\Entity\TimetableLesson;

readonly class ExcuseStatusItem {

    public function __construct(private DateLesson $dateLesson, private ?Attendance $attendance, private ?StudentAbsence $absence, private ?TimetableLesson $timetableLesson, private ?LessonEntry $entry) {

    }

    /**
     * @return DateLesson
     */
    public function getDateLesson(): DateLesson {
        return $this->dateLesson;
    }

    /**
     * @return Attendance|null
     */
    public function getAttendance(): ?Attendance {
        return $this->attendance;
    }

    public function getAbsence(): ?StudentAbsence {
        return $this->absence;
    }

    /**
     * @return LessonEntry|null
     */
    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    /**
     * @return TimetableLesson|null
     */
    public function getTimetableLesson(): ?TimetableLesson {
        return $this->timetableLesson;
    }

    public function isExcused(): bool {
        if($this->entry !== null && $this->entry->isCancelled()) {
            return true;
        }

        if($this->timetableLesson === null) {
            return true;
        }

        if($this->absence->getType()->isMustApprove()) {
            return $this->absence->isApproved();
        }

        if($this->absence->getType()->getBookExcuseStatus() === AttendanceExcuseStatus::Excused) {
            return true;
        }

        if($this->attendance->getAssociatedExcuses()->count() > 0) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getType() !== AttendanceType::Absent) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->isZeroAbsentLesson()) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getExcuseStatus() === AttendanceExcuseStatus::Excused) {
            return true;
        }

        return false;
    }
}