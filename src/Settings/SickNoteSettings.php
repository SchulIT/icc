<?php

namespace App\Settings;

use App\Markdown\Processor\HeadingAnchorProcessor;

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

    public function getIntroductionText(): ?string {
        return $this->getValue('sick_note.introduction_text');
    }

    public function setIntroductionText(?string $text): void {
        $this->setValue('sick_note.introduction_text', $text);
    }

    public function getRetentionDays(): int {
        return $this->getValue('sick_note.retention_days', 0);
    }

    public function setRetentionDays(int $days): void {
        $this->setValue('sick_note.retention_days', $days);
    }

    public function getOrderedByHelp(): ?string {
        return $this->getValue('sick_note.ordered_by.help');
    }

    public function setOrderedByHelp(?string $help): void {
        $this->setValue('sick_note.ordered_by.help', $help);
    }
}
