<?php

namespace App\Notification;

use App\Entity\StudentAbsence;
use App\Entity\User;

class StudentAbsenceNotification extends Notification {
    public function __construct(User $recipient, string $subject, string $content, ?string $link, ?string $linkText, private readonly StudentAbsence $absence) {
        parent::__construct($recipient, $subject, $content, $link, $linkText, true);
    }
    
    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}