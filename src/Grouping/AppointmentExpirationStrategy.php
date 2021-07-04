<?php

namespace App\Grouping;

use App\Entity\Appointment;
use SchulIT\CommonBundle\Helper\DateHelper;

class AppointmentExpirationStrategy implements GroupingStrategyInterface {

    private $dateHelper;

    public function __construct(DateHelper $dateHelper) {
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param Appointment $appointment
     * @return bool
     */
    public function computeKey($appointment, array $options = [ ]) {
        $today = $this->dateHelper->getToday();
        $now = $this->dateHelper->getNow();

        if($appointment->getEnd() !== null) {
            if(($appointment->isAllDay() && $appointment->getEnd() < $today) || (!$appointment->isAllDay() && $appointment->getEnd() < $now)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $keyA
     * @param bool $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param bool $key
     * @return GroupInterface
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new AppointmentExpirationGroup($key);
    }
}