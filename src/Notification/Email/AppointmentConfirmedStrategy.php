<?php

namespace App\Notification\Email;

use App\Notification\AppointmentConfirmedNotification;
use App\Notification\Notification;

readonly class AppointmentConfirmedStrategy implements EmailStrategyInterface {

    public function supports(Notification $notification): bool {
        return $notification instanceof AppointmentConfirmedNotification;
    }

    /**
     * @param AppointmentConfirmedNotification $notification
     * @return string|null
     */
    public function getReplyTo(Notification $notification): ?string {
        return $notification->getConfirmedBy()->getEmail();
    }

    /**
     * @param AppointmentConfirmedNotification $notification
     * @return string
     */
    public function getSender(Notification $notification): string {
        return sprintf('%s %s', $notification->getConfirmedBy()->getFirstname(), $notification->getConfirmedBy()->getLastname());
    }

    public function getTemplate(): string {
        return 'email/default.txt';
    }

    public function getHtmlTemplate(): ?string {
        return 'email/default.html.twig';
    }
}