<?php

namespace App\Feature;

use App\Settings\FeatureSettings;

readonly class FeatureManager {

    public function __construct(private FeatureSettings $settings) {

    }

    public function isFeatureEnabled(Feature $feature): bool {
        return $this->settings->isFeatureEnabled($feature);
    }


}