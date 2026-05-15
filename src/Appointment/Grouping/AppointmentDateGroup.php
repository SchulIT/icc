<?php

namespace App\Appointment\Grouping;

use App\Appointment\Entity\Appointment;
use App\Framework\Grouping\GroupInterface;

/**
 * @implements GroupInterface<int, Appointment>
 */
class AppointmentDateGroup implements GroupInterface {

    /**
     * @var Appointment[]
     */
    private array $appointments = [ ];

    public function __construct(
        private readonly int $key,
        private readonly int $month,
        private readonly int $year
    ) { }

    public function getKey(): int {
        return $this->key;
    }

    public function addItem($item): void {
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
    public function getAppointments(): array {
        return $this->appointments;
    }
}