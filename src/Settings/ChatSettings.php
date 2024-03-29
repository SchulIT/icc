<?php

namespace App\Settings;

use App\Entity\UserType;

class ChatSettings extends AbstractSettings {
    public function getEnabledUserTypes(): array {
        return $this->getValue('chat.enabled.user_types', [ ]);
    }

    public function setEnabledUserTypes(array $userTypes): void {
        $this->setValue('chat.enabled.user_types', $userTypes);
    }

    public function getAllowedRecipients(UserType $userType): array {
        return $this->getValue(sprintf('chat.%s.recipient_types', $userType->value), []);
    }

    public function setAllowedRecipients(UserType $userType, array $recipientUserTypes): void {
        $this->setValue(sprintf('chat.%s.recipient_types', $userType->value), $recipientUserTypes);
    }
}