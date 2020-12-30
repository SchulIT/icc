<?php

namespace App\Dashboard;

use App\Entity\ResourceReservation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RoomReservationViewItem extends AbstractViewItem {

    private $reservation;

    private $violations;

    public function __construct(ResourceReservation $reservation, ConstraintViolationListInterface $violations) {
        $this->reservation = $reservation;
        $this->violations = $violations;
    }

    /**
     * @return ResourceReservation
     */
    public function getReservation(): ResourceReservation {
        return $this->reservation;
    }

    /**
     * @return ConstraintViolationListInterface
     */
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