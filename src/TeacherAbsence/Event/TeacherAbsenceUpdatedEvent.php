<?php

namespace App\TeacherAbsence\Event;

use App\TeacherAbsence\Entity\TeacherAbsence;
use Symfony\Contracts\EventDispatcher\Event;

class TeacherAbsenceUpdatedEvent extends Event {
    public function __construct(private readonly TeacherAbsence $absence) { }

    public function getAbsence(): TeacherAbsence {
        return $this->absence;
    }
}