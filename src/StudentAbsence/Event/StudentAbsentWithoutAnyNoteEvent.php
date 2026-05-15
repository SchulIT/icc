<?php

namespace App\StudentAbsence\Event;

use App\Book\Entity\Attendance;
use Symfony\Contracts\EventDispatcher\Event;

class StudentAbsentWithoutAnyNoteEvent extends Event {
    public function __construct(private readonly Attendance $attendance) {

    }

    public function getAttendance(): Attendance {
        return $this->attendance;
    }
}