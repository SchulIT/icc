<?php

namespace App\Event;

use App\Entity\StudentAbsenceMessage;

class StudentAbsenceMessageCreatedEvent extends AbstractStudentAbsenceEvent {
    private StudentAbsenceMessage $message;

    public function __construct(StudentAbsenceMessage $message) {
        parent::__construct($message->getAbsence());

        $this->message = $message;
    }

    /**
     * @return StudentAbsenceMessage
     */
    public function getMessage(): StudentAbsenceMessage {
        return $this->message;
    }
}