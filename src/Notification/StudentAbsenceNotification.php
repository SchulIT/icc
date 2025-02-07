<?php

namespace App\Notification;

use App\Entity\StudentAbsence;
use App\Entity\User;

class StudentAbsenceNotification extends Notification {
    public function __construct(string $type, User $recipient, string $subject, string $content, ?string $link, ?string $linkText, private readonly StudentAbsence $absence) {
        parent::__construct($type, $recipient, $subject, $content, $link, $linkText);
    }
    
    public function getAbsence(): StudentAbsence {
        return $this->absence;
    }
}