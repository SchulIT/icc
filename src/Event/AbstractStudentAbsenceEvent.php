<?php

namespace App\Event;

use App\Entity\StudentAbsence;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractStudentAbsenceEvent extends Event {
    public function __construct(private StudentAbsence $absence)
    {
    }

    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}