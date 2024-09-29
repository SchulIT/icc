<?php

namespace App\Dashboard;

use App\Entity\BookEvent;
use App\Entity\Student;

class AbsentBookEventStudent extends AbsentStudent {
    public function __construct(Student $student, private readonly BookEvent $bookEvent) {
        parent::__construct($student, AbsenceReason::BookEvent);
    }

    public function getBookEvent(): BookEvent {
        return $this->bookEvent;
    }
}