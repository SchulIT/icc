<?php

namespace App\Settings;

use App\Feature\Feature;

class FeatureSettings extends AbstractSettings {
    public function isFeatureEnabled(Feature $feature): bool {
        return $this->getValue(sprintf('feature.%s.enabled', $feature->value), false);
    }

    public function setFeatureEnabled(Feature $feature, bool $value): void {
        $this->setValue(sprintf('feature.%s.enabled', $feature->value), $value);
    }
}