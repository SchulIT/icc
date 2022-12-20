<?php

namespace App\Settings;

use DateTime;
use App\Entity\UserType;

class AppointmentsSettings extends AbstractSettings {
    public const StartDate = 'start';
    public const EndDate = 'end';

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getKeyName(UserType $userType, $settingsType) {
        return sprintf('appointments.%s.%s', strtolower($userType->value), strtolower($settingsType));
    }

    public function getStart(UserType $userType) {
        $key = $this->getKeyName($userType, AppointmentsSettings::StartDate);
        return $this->getValue($key);
    }

    public function getEnd(UserType $userType) {
        $key = $this->getKeyName($userType, AppointmentsSettings::EndDate);
        return $this->getValue($key);
    }

    public function setStart(UserType $userType, DateTime $dateTime = null) {
        $key = $this->getKeyName($userType, AppointmentsSettings::StartDate);
        $this->setValue($key, $dateTime);
    }

    public function setEnd(UserType $userType, DateTime $dateTime = null) {
        $key = $this->getKeyName($userType, AppointmentsSettings::EndDate);
        $this->setValue($key, $dateTime);
    }

    public function getExamColor(): ?string {
        return $this->getValue('appointments.exam_color', null);
    }

    public function setExamColor(?string $color): void {
        $this->setValue('appointments.exam_color', $color);
    }
}