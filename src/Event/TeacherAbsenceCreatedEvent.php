<?php

namespace App\Event;

use App\Entity\TeacherAbsence;
use Symfony\Contracts\EventDispatcher\Event;

class TeacherAbsenceCreatedEvent extends Event {
    public function __construct(private readonly TeacherAbsence $absence) { }

    public function getAbsence(): TeacherAbsence {
        return $this->absence;
    }
}