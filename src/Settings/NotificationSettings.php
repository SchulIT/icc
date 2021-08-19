<?php

namespace App\Settings;

class NotificationSettings extends AbstractSettings {

    public function getEmailEnabledUserTypes(): array {
        return $this->getValue('notifications.email.user_types', [ ]);
    }

    public function setEmailEnabledUserTypes(array $userTypes): void {
        $this->setValue('notifications.email.user_types', $userTypes);
    }


}