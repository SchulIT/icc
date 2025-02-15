<?php

namespace App\Settings;

class GeneralSettings extends AbstractSettings {

    public function getCurrentSectionId(): ?int {
        return $this->getValue('general.current_section', null);
    }

    public function setCurrentSectionId(int $sectionId): void {
        $this->setValue('general.current_section', $sectionId);
    }

    public function getCustomCSS(): ?string {
        return $this->getValue('general.custom_css', null);
    }

    public function setCustomCSS(?string $customCSS): void {
        $this->setValue('general.custom_css', $customCSS);
    }

}