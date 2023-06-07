<?php

namespace App\Settings;

class NotificationSettings extends AbstractSettings {

    public function getEmailEnabledUserTypes(): array {
        return $this->getValue('notifications.email.user_types', [ ]);
    }

    public function setEmailEnabledUserTypes(array $userTypes): void {
        $this->setValue('notifications.email.user_types', $userTypes);
    }

    public function getPushoverEnabledUserTypes(): array {
        return $this->getValue('notifications.pushover.user_types', [ ]);
    }

    public function setPushoverEnabledUserTypes(array $userTypes): void {
        $this->setValue('notifications.pushover.user_types', $userTypes);
    }

    public function getPushoverApiToken(): ?string {
        return $this->getValue('notifications.pushover.token', null);
    }

    public function setPushoverApiToken(?string $apiToken): void {
        $this->setValue('notifications.pushover.token', $apiToken);
    }
}