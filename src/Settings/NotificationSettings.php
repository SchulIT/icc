<?php

namespace App\Settings;

use App\Entity\UserType;
use App\Notification\Delivery\DeliverStrategyType;
use App\Notification\NotificationDeliveryTarget;

class NotificationSettings extends AbstractSettings {

    public function isNotificationsEnabled(): bool {
        return $this->getValue('notifications.enabled', true);
    }

    public function setNotificationsEnabled(bool $isEnabled): void {
        $this->setValue('notifications.enabled', $isEnabled);
    }

    public function isEmailEnabled(): bool {
        return $this->getValue('notifications.email.enabled', false);
    }

    public function setEmailEnabled(bool $isEnabled): void {
        $this->setValue('notifications.email.enabled', $isEnabled);
    }

    public function isPushoverEnabled(): bool {
        return $this->getValue('notifications.pushover.enabled', false);
    }

    public function setPushoverEnabled(bool $isEnabled): void {
        $this->setValue('notifications.pushover.enabled', $isEnabled);
    }

    public function setDeliveryStrategy(UserType $userType, string $notifierKey, NotificationDeliveryTarget $target, DeliverStrategyType $strategy): void {
        $this->setValue(sprintf('notifications.delivery_strategy.%s.%s.%s', $notifierKey, $target->value, $userType->value), $strategy);
    }

    public function getDeliveryStrategy(UserType $userType, string $notifierKey, NotificationDeliveryTarget $target): DeliverStrategyType {
        return $this->getValue(sprintf('notifications.delivery_strategy.%s.%s.%s', $notifierKey, $target->value, $userType->value), DeliverStrategyType::Never);
    }
}