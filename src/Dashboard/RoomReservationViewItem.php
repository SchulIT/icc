<?php

namespace App\Dashboard;

use App\Entity\ResourceReservation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RoomReservationViewItem extends AbstractViewItem {

    public function __construct(private ResourceReservation $reservation, private ConstraintViolationListInterface $violations)
    {
    }

    public function getReservation(): ResourceReservation {
        return $this->reservation;
    }

    public function getViolations(): ConstraintViolationListInterface {
        return $this->violations;
    }

    public function hasViolations(): bool {
        return count($this->violations) > 0;
    }

    public function getBlockName(): string {
        return 'reservation';
    }
}