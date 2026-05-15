<?php

namespace App\Appointment\Grouping;

use App\Appointment\Entity\Appointment;
use App\Framework\Grouping\GroupInterface;

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