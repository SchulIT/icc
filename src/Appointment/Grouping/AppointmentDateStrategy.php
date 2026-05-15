<?php

namespace App\Appointment\Grouping;

use App\Appointment\Entity\Appointment;
use App\Appointment\Grouping\AppointmentDateGroup;
use App\Framework\Grouping\GroupingStrategyInterface;
use App\Framework\Grouping\GroupInterface;

class AppointmentDateStrategy implements GroupingStrategyInterface {

    /**
     * @param Appointment $appointment
     * @return int
     */
    public function computeKey($appointment, array $options = [ ]) {
        return (int)$appointment->getStart()->format('Ym');
    }

    /**
     * @param int $keyA
     * @param int $keyB
     */
    public function areEqualKeys($keyA, $keyB, array $options = [ ]): bool {
        return $keyA === $keyB;
    }

    /**
     * @param int $key
     */
    public function createGroup($key, array $options = [ ]): GroupInterface {
        $year = (int)($key / 100);
        $month = $key % 100;

        return new AppointmentDateGroup($key, $month, $year);
    }
}