<?php

namespace App\StudentAbsence\Event;

use App\StudentAbsence\Entity\StudentAbsenceMessage;
use App\StudentAbsence\Event\AbstractStudentAbsenceEvent;

class StudentAbsenceMessageCreatedEvent extends AbstractStudentAbsenceEvent {
    private StudentAbsenceMessage $message;

    public function __construct(StudentAbsenceMessage $message) {
        parent::__construct($message->getAbsence());

        $this->message = $message;
    }

    public function getMessage(): StudentAbsenceMessage {
        return $this->message;
    }
}