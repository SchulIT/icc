<?php

namespace App\StudentAbsence;

use App\Book\Student\ExcuseCollection;
use App\Entity\DateLesson;
use App\Entity\Attendance;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\StudentAbsence;
use App\Entity\TimetableLesson;

class ExcuseStatusItem {

    public function __construct(private readonly DateLesson $dateLesson, private readonly ?ExcuseCollection $excuseCollection, private readonly ?Attendance $attendance, private readonly ?StudentAbsence $absence, private readonly ?TimetableLesson $timetableLesson, private readonly ?LessonEntry $entry) {

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

        if($this->excuseCollection !== null && count($this->excuseCollection) > 0) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getType() !== AttendanceType::Absent) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getAbsentLessons() === 0) {
            return true;
        }

        if($this->attendance !== null && $this->attendance->getExcuseStatus() === AttendanceExcuseStatus::Excused) {
            return true;
        }

        return false;
    }
}