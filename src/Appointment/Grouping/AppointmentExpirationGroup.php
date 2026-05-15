<?php

namespace App\Appointment\Grouping;

use App\Appointment\Entity\Appointment;
use App\Framework\Grouping\GroupInterface;

/**
 * @implements GroupInterface<bool, Appointment>
 */
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
    public function getAppointments(): array {
        return $this->appointments;
    }

    public function getKey(): bool {
        return $this->isExpired;
    }

    public function addItem($item): void {
        $this->appointments[] = $item;
    }
}