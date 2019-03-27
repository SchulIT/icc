<?php

namespace App\Grouping;

use App\Entity\Appointment;

class AppointmentDateGroup implements GroupInterface {

    /** @var int */
    private $month;

    /** @var int */
    private $year;

    /** @var int */
    private $key;

    /**
     * @var Appointment[]
     */
    private $appointments = [ ];

    public function __construct(int $key, int $month, int $year) {
        $this->key = $key;
        $this->month = $month;
        $this->year = $year;
    }

    public function getKey() {
        return $this->key;
    }

    public function addItem($item) {
        $this->appointments[] = $item;
    }

    /**
     * @return int
     */
    public function getMonth(): int {
        return $this->month;
    }

    /**
     * @return int
     */
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