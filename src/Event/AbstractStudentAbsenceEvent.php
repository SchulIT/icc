<?php

namespace App\Event;

use App\Entity\StudentAbsence;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractStudentAbsenceEvent extends Event {
    private StudentAbsence $absence;

    public function __construct(StudentAbsence $absence) {
        $this->absence = $absence;
    }

    /**
     * @return StudentAbsence
     */
    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}