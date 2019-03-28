<?php

namespace App\Grouping;

use App\Entity\Appointment;

class AppointmentDateStrategy implements GroupingStrategyInterface {

    /**
     * @param Appointment $appointment
     * @return int
     */
    public function computeKey($appointment) {
        return (int)$appointment->getStart()->format('Ym');
    }

    /**
     * @param int $keyA
     * @param int $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param int $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        $year = (int)($key / 100);
        $month = $key % 100;

        return new AppointmentDateGroup($key, $month, $year);
    }
}