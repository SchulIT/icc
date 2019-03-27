<?php

namespace App\Settings;

use App\Entity\UserType;

class AppointmentsSettings extends AbstractSettings {
    const StartDate = 'start';
    const EndDate = 'end';

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getKeyName(UserType $userType, $settingsType) {
        return sprintf('appointments.%s.%s', strtolower($userType->getValue()), strtolower($settingsType));
    }

    public function getStart(UserType $userType) {
        $key = $this->getKeyName($userType->getValue(), AppointmentsSettings::StartDate);
        return $this->getValue($key);
    }

    public function getEnd(UserType $userType) {
        $key = $this->getKeyName($userType->getValue(), AppointmentsSettings::EndDate);
        return $this->getValue($key);
    }

    public function setStart(UserType $userType, \DateTime $dateTime = null) {
        $key = $this->getKeyName($userType->getValue(), AppointmentsSettings::StartDate);
        $this->setValue($key, $dateTime);
    }

    public function setEnd(UserType $userType, \DateTime $dateTime = null) {
        $key = $this->getKeyName($userType->getValue(), AppointmentsSettings::EndDate);
        $this->setValue($key, $dateTime);
    }
}