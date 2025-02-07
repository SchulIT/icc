<?php

namespace App\Notification;

use App\Entity\Appointment;
use App\Entity\User;

class AppointmentConfirmedNotification extends AppointmentNotification {
    public function __construct(string $key, User $recipient, string $subject, string $content, ?string $link, ?string $linkText, Appointment $appointment, private readonly User $confirmedBy) {
        parent::__construct($key, $recipient, $subject, $content, $link, $linkText, $appointment);
    }

    public function getConfirmedBy(): User {
        return $this->confirmedBy;
    }
}