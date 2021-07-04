<?php

namespace App\Settings;

class GeneralSettings extends AbstractSettings {

    public function getCurrentSectionId(): ?int {
        return $this->getValue('general.current_section', null);
    }

    public function setCurrentSectionId(int $sectionId): void {
        $this->setValue('general.current_section', $sectionId);
    }

}