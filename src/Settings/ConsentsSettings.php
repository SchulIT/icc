<?php

namespace App\Settings;

class ConsentsSettings extends AbstractSettings {

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function showPrivacyConsents(): bool {
        return $this->getValue('consents.students.privacy.enabled', true);
    }

    public function setShowPrivacyConsents(bool $value): void {
        $this->setValue('consents.students.privacy.enabled', $value);
    }

    public function showLmsConsents(): bool {
        return $this->getValue('consents.students.lms.enabled', true);
    }

    public function setShowLmsConsents(bool $value): void {
        $this->setValue('consents.students.lms.enabled', $value);
    }

    public function setInfoText(string|null $text): void {
        $this->setValue('consents.students.info', $text);
    }

    public function getInfoText(): ?string {
        return $this->getValue('consents.students.info', null);
    }
}