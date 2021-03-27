<?php

namespace App\Settings;

class GeneralSettings extends AbstractSettings {

    public function getSchoolYear(): int {
        return $this->getValue('general.school_year', 2020);
    }

    public function setSchoolYear(int $schoolYear): void {
        $this->setValue('general.school_year', $schoolYear);
    }

    public function getSection(): int {
        return $this->getValue('general.current_section', 1);
    }

    public function setSection(int $section): void {
        $this->setValue('general.current_section', $section);
    }

    public function getSectionName(): ?string {
        return $this->getValue('general.section_name', 'Halbjahr');
    }

    public function setSectionName(string $name): void {
        $this->setValue('general.section_name', $name);
    }

}