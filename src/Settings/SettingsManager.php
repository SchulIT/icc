<?php

namespace App\Settings;

use App\Entity\Setting;
use App\Repository\SettingRepositoryInterface;

/**
 * Manager for application settings which can be configured online
 */
class SettingsManager {

    private $repository;

    private $initialised = false;

    /** @var Setting[] */
    private $settings = [ ];

    public function __construct(SettingRepositoryInterface $settingRepository) {
        $this->repository = $settingRepository;
    }

    /**
     * Gets the value of a setting or $default if setting does not exist
     *
     * @param string $key
     * @param mixed $default Default value which is returned if the setting with key $key is non-existent
     * @return mixed|null
     */
    public function getValue($key, $default = null) {
        $this->initializeIfNecessary();

        if(isset($this->settings[$key])) {
            return $this->settings[$key]->getValue();
        }

        return $default;
    }

    /**
     * Sets the value of a setting
     *
     * @param string $key
     * @param mixed $value
     */
    public function setValue($key, $value) {
        $this->initializeIfNecessary();

        if(!isset($this->settings[$key])) {
            $this->settings[$key] = (new Setting())
                ->setKey($key);
        }

        $setting = $this->settings[$key];
        $setting->setValue($value);

        $this->repository
            ->persist($setting);
    }

    /**
     * Checks whether to load all settings from the database and loads them if necessary
     */
    private function initializeIfNecessary() {
        if($this->initialised !== true) {
            $this->initialize();
        }
    }

    /**
     * Loads all settings from the database
     */
    protected function initialize() {
        $settings = $this->repository
            ->findAll();

        foreach($settings as $setting) {
            $this->settings[$setting->getKey()] = $setting;
        }

        $this->initialised = true;
    }
}