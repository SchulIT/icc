<?php

namespace App\Appointment\Settings;

use App\Framework\Settings\AbstractSettings;
use App\Framework\Settings\SettingsManager;
use DateTime;
use App\Common\Entity\UserType;

class AppointmentsSettings extends AbstractSettings {
    public const string StartDate = 'start';
    public const string EndDate = 'end';

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getKeyName(UserType $userType, $settingsType): string {
        return sprintf('appointments.%s.%s', strtolower($userType->value), strtolower($settingsType));
    }

    public function getStart(UserType $userType): DateTime|null {
        $key = $this->getKeyName($userType, AppointmentsSettings::StartDate);
        return $this->getValue($key);
    }

    public function getEnd(UserType $userType): DateTime|null {
        $key = $this->getKeyName($userType, AppointmentsSettings::EndDate);
        return $this->getValue($key);
    }

    public function setStart(UserType $userType, DateTime|null $dateTime = null): void {
        $key = $this->getKeyName($userType, AppointmentsSettings::StartDate);
        $this->setValue($key, $dateTime);
    }

    public function setEnd(UserType $userType, DateTime|null $dateTime = null): void {
        $key = $this->getKeyName($userType, AppointmentsSettings::EndDate);
        $this->setValue($key, $dateTime);
    }

    public function getExamColor(): ?string {
        return $this->getValue('appointments.exam_color', null);
    }

    public function setExamColor(?string $color): void {
        $this->setValue('appointments.exam_color', $color);
    }

    public function getImportCountry(): string {
        return $this->getValue('appointments.import.country', 'DE');
    }

    public function setImportCountry(string $country): void {
        $this->setValue('appointments.import.country', $country);
    }

    public function getImportSubdivision(): string {
        return $this->getValue('appointments.import.subdivision', 'DE-NW');
    }

    public function setImportSubdivision(string $subdivision): void {
        $this->setValue('appointments.import.subdivision', $subdivision);
    }

    public function getImportAppointmentCategoryId(): int|null {
        return $this->getValue('appointments.import.category_id', null);
    }

    public function setImportAppointmentCategoryId(int $id): void {
        $this->setValue('appointments.import.category_id', $id);
    }

}
