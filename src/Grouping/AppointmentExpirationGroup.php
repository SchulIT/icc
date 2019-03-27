<?php

namespace App\Grouping;

use App\Entity\Appointment;

class AppointmentExpirationGroup implements GroupInterface {

    /** @var bool  */
    private $isExpired;

    /** @var Appointment[] */
    private $appointments = [ ];

    public function __construct(bool $isExpired) {
        $this->isExpired = $isExpired;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool {
        return $this->isExpired;
    }

    /**
     * @return Appointment[]
     */
    public function getAppointments() {
        return $this->appointments;
    }

    public function getKey() {
        return $this->isExpired;
    }

    public function addItem($item) {
        $this->appointments[] = $item;
    }
}