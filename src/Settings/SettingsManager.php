<?php

namespace App\Settings;

use App\Entity\Setting;
use App\Repository\SettingRepositoryInterface;
use DateTime;
use DateTimeInterface;
use Exception;
use UnitEnum;

/**
 * Manager for application settings which can be configured online
 */
class SettingsManager {

    private bool $initialised = false;

    /** @var Setting[] */
    private array $settings = [ ];

    public function __construct(private readonly SettingRepositoryInterface $repository)
    {
    }

    /**
     * Gets the value of a setting or $default if setting does not exist
     *
     * @param string $key
     * @param mixed $default Default value which is returned if the setting with key $key is non-existent
     * @return mixed|null
     */
    public function getValue(string $key, mixed $default = null): mixed {
        $this->initializeIfNecessary();

        if(!isset($this->settings[$key])) {
            return $default;
        }

        $value = $this->settings[$key]->getValue();
        try {
            return unserialize($value);
        } catch(Exception) {
            return $default;
        }
    }

    /**
     * Sets the value of a setting
     *
     * @param string $key
     * @param mixed $value
     */
    public function setValue(string $key, mixed $value): void {
        $this->initializeIfNecessary();

        if(!isset($this->settings[$key])) {
            $this->settings[$key] = (new Setting())
                ->setKey($key);
        }

        $setting = $this->settings[$key];
        $setting->setValue(serialize($value));

        $this->repository
            ->persist($setting);
    }

    /**
     * Checks whether to load all settings from the database and loads them if necessary
     */
    private function initializeIfNecessary(): void {
        if($this->initialised !== true) {
            $this->initialize();
        }
    }

    /**
     * Loads all settings from the database
     */
    protected function initialize(): void {
        $settings = $this->repository
            ->findAll();

        foreach($settings as $setting) {
            $this->settings[$setting->getKey()] = $setting;
        }

        $this->initialised = true;
    }
}