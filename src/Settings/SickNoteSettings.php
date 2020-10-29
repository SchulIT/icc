<?php

namespace App\Settings;

class SickNoteSettings extends AbstractSettings {

    public function isEnabled(): bool {
        return $this->getValue('sick_note.enabled', false);
    }

    public function setEnabled(bool $enabled): void {
        $this->setValue('sick_note.enabled', $enabled);
    }

    public function getRecipient(): ?string {
        return $this->getValue('sick_note.recipient', null);
    }

    public function setRecipient(?string $recipient): void {
        $this->setValue('sick_note.recipient', $recipient);
    }

    public function getPrivacyUrl(): ?string {
        return $this->getValue('sick_note.privacy_url', null);
    }

    public function setPrivacyUrl(?string $url): void {
        $this->setValue('sick_note.privacy_url', $url);
    }
}