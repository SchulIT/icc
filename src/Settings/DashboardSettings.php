<?php

namespace App\Settings;

class DashboardSettings extends AbstractSettings {
    public function __construct(SettingsManager $settingsManager) {
        parent::__construct($settingsManager);
    }

    public function getRemovableSubstitutionTypes(): array {
        return $this->getValue('dashboard.substitutions.removable_types', [ ]);
    }

    public function setRemovableSubstitutionTypes(array $types): void {
        $this->setValue('dashboard.substitutions.removable_types', $types);
    }

    public function getAdditionalSubstitutionTypes(): array {
        return $this->getValue('dashboard.substitutions.additional_types', [ ]);
    }

    public function setAdditionalSubstitutionTypes(array $types): void {
        $this->setValue('dashboard.substitutions.additional_types', $types);
    }
}