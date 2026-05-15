<?php

namespace App\Appointment\Grouping;

use App\Appointment\Entity\Appointment;
use App\Framework\Grouping\GroupInterface;

class AppointmentDateGroup implements GroupInterface {

    /**
     * @var Appointment[]
     */
    private array $appointments = [ ];

    public function __construct(private int $key, private int $month, private int $year)
    {
    }

    public function getKey() {
        return $this->key;
    }

    public function addItem($item) {
        $this->appointments[] = $item;
    }

    public function getMonth(): int {
        return $this->month;
    }

    public function getYear(): int {
        return $this->year;
    }

    /**
     * @return Appointment[]
     */
    public function getAppointments() {
        return $this->appointments;
    }
}