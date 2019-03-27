<?php

namespace App\Grouping;

use App\Entity\Appointment;
use SchoolIT\CommonBundle\Helper\DateHelper;

class AppointmentExpirationStrategy implements GroupingStrategyInterface {

    private $dateHelper;

    public function __construct(DateHelper $dateHelper) {
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param Appointment $appointment
     * @return bool
     */
    public function computeKey($appointment) {
        $now = $this->dateHelper->getNow();

        if($appointment->getEnd() !== null && $appointment->getEnd() < $now) {
            return true;
        }

        return false;
    }

    /**
     * @param bool $keyA
     * @param bool $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param bool $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new AppointmentExpirationGroup($key);
    }
}