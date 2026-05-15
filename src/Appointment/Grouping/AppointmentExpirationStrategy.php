<?php

namespace App\Appointment\Grouping;

use App\Appointment\Entity\Appointment;
use App\Appointment\Grouping\AppointmentExpirationGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;
use SchulIT\CommonBundle\Helper\DateHelper;

class AppointmentExpirationStrategy implements GroupingStrategyInterface {

    public function __construct(private DateHelper $dateHelper)
    {
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
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param bool $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        return new AppointmentExpirationGroup($key);
    }
}