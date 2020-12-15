<?php

namespace App\Dashboard;

use App\Entity\SickNote;
use App\Entity\Student;

class AbsentSickStudent extends AbsentStudent {

    private $sickNote;

    public function __construct(Student $student, SickNote $sickNote) {
        parent::__construct($student, AbsenceReason::Sick());

        $this->sickNote = $sickNote;
    }

    /**
     * @return SickNote
     */
    public function getSickNote(): SickNote {
        return $this->sickNote;
    }
}