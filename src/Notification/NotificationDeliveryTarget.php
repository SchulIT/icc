<?php

namespace App\Notification;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum NotificationDeliveryTarget: string implements TranslatableInterface {
    case Email = 'email';
    case Pushover = 'pushover';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(sprintf('notifications.target.%s', $this->value), locale: $locale);
    }
}