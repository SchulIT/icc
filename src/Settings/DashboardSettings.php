<?php

namespace App\Settings;

use Symfony\Component\Validator\Constraints\DateTime;

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

    public function getFreeLessonSubstitutionTypes(): array {
        return $this->getValue('dashboard.substitutions.free_lesson_types', [ ]);
    }

    public function setFreeLessonSubstitutionTypes(array $types): void {
        $this->setValue('dashboard.substitutions.free_lesson_types', $types);
    }

    public function getNextDayThresholdTime() {
        return $this->getValue('dashboard.next_day_threshold', null);
    }

    public function setNextDayThresholdTime($threshold): void {
        $this->setValue('dashboard.next_day_threshold', $threshold);
    }

    public function skipWeekends(): bool {
        return $this->getValue('dashboard.skip_weekends', false);
    }

    public function setSkipWeekends(bool $skipWeekends): void {
        $this->setValue('dashboard.skip_weekends', $skipWeekends);
    }
}