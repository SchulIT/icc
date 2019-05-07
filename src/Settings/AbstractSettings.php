<?php

namespace App\Settings;

abstract class AbstractSettings {
    private $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        $this->settingsManager = $settingsManager;
    }

    protected function getValue($key, $default = null) {
        return $this->settingsManager
            ->getValue($key, $default);
    }

    protected function setValue($key, $value): void {
        $this->settingsManager
            ->setValue($key, $value);
    }
}