<?php

namespace App\Settings;

use App\Markdown\Processor\HeadingAnchorProcessor;

class StudentAbsenceSettings extends AbstractSettings {

    public function isEnabled(): bool {
        return $this->getValue('student_absences.enabled', false);
    }

    public function setEnabled(bool $enabled): void {
        $this->setValue('student_absences.enabled', $enabled);
    }

    public function getPrivacyUrl(): ?string {
        return $this->getValue('student_absences.privacy_url', null);
    }

    public function setPrivacyUrl(?string $url): void {
        $this->setValue('student_absences.privacy_url', $url);
    }

    public function getIntroductionText(): ?string {
        return $this->getValue('student_absences.introduction_text');
    }

    public function setIntroductionText(?string $text): void {
        $this->setValue('student_absences.introduction_text', $text);
    }

    public function getRetentionDays(): int {
        return $this->getValue('student_absences.retention_days', 0);
    }

    public function setRetentionDays(int $days): void {
        $this->setValue('student_absences.retention_days', $days);
    }

    public function getNextDayThresholdTime(): ?string {
        return $this->getValue('student_absences.next_day.threshold', null);
    }

    public function setNextDayThresholdTime(?string $time): void {
        $this->setValue('student_absences.next_day.threshold', $time);
    }
}
