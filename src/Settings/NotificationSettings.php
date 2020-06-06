<?php

namespace App\Settings;

class NotificationSettings extends AbstractSettings {

    public function getPushEnabledUserTypes(): array {
        return $this->getValue('notifications.web_push.user_types', [ ]);
    }

    public function setPushEnabledUserTypes(array $userTypes): void {
        $this->setValue('notifications.web_push.user_types', $userTypes);
    }

    public function getEmailEnabledUserTypes(): array {
        return $this->getValue('notifications.email.user_types', [ ]);
    }

    public function setEmailEnabledUserTypes(array $userTypes): void {
        $this->setValue('notifications.email.user_types', $userTypes);
    }


}