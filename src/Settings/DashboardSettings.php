<?php

namespace App\Settings;

class DashboardSettings extends AbstractSettings {

    public const AheadDaysKeys = 'dashboard.days_ahead';
    public const SkipWeekends = 'dashboard.skip_weekends';

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
}