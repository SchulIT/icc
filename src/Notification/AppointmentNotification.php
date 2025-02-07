<?php

namespace App\Notification;

use App\Entity\Appointment;
use App\Entity\User;

class AppointmentNotification extends Notification {
    public function __construct(string $type, User $recipient, string $subject, string $content, ?string $link, ?string $linkText, private readonly Appointment $appointment) {
        parent::__construct($type, $recipient, $subject, $content, $link, $linkText);
    }

    public function getAppointment(): Appointment {
        return $this->appointment;
    }
}