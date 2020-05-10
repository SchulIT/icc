<?php

namespace App\Settings;

class SubstitutionSettings extends AbstractSettings {

    public const AheadDaysKeys = 'substitutions.days_ahead';
    public const SkipWeekends = 'substitutions.skip_weekends';

    public function getNumberOfAheadDaysForSubstitutions(): int {
        return (int)$this->getValue(static::AheadDaysKeys, 7);
    }

    public function setNumberOfAheadDaysForSubstitutions(int $days): void {
        $this->setValue(static::AheadDaysKeys, $days);
    }

    public function skipWeekends(): bool  {
        return (bool)$this->getValue(static::SkipWeekends, false);
    }

    public function setSkipWeekends(bool $skipWeekends): void {
        $this->setValue(static::SkipWeekends, $skipWeekends);
    }

    public function isNotificationsEnabled(): bool {
        return (bool)$this->getValue('substitutions.notifications.enabled', false);
    }

    public function setNotificationsEnabled(bool $enabled): void {
        $this->setValue('substitutions.notifications.enabled', $enabled);
    }

    public function getNotificationReplyToAddress(): ?string {
        return (string)$this->getValue('substitutions.notifications.reply_to', null);
    }

    public function setNotificationReplyToAddress(?string $address): void {
        $this->setValue('substitutions.notifications.reply_to', $address);
    }

    public function getNotificationSender(): ?string {
        return (string)$this->getValue('substitutions.notifications.sender', null);
    }

    public function setNotificationSender(?string $sender): void {
        $this->setValue('substitutions.notifications.sender', $sender);
    }
}