<?php

namespace App\Grouping;

use App\Entity\Appointment;

class AppointmentExpirationGroup implements GroupInterface {

    /** @var Appointment[] */
    private array $appointments = [ ];

    public function __construct(private bool $isExpired)
    {
    }

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