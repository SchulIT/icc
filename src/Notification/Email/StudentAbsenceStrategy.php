<?php

namespace App\Notification\Email;

use App\Notification\Notification;
use App\Notification\StudentAbsenceNotification;

readonly class StudentAbsenceStrategy implements EmailStrategyInterface {


    public function supports(Notification $notification): bool {
        return $notification instanceof StudentAbsenceNotification;
    }

    public function getReplyTo(Notification $notification): ?string {
        return null;
    }

    /**
     * @param StudentAbsenceNotification $notification
     * @return string
     */
    public function getSender(Notification $notification): string {
        return '';
    }

    public function getTemplate(): string {
        return 'email/default.txt.twig';
    }

    public function getHtmlTemplate(): ?string {
        return 'email/default.html.twig';
    }
}