<?php

namespace App\Repository;

use App\Entity\AppointmentVisibility;

interface AppointmentVisibilityRepositoryInterface {

    /**
     * @return AppointmentVisibility[]
     */
    public function findAll(): array;

    /**
     * @param AppointmentVisibility $visibility
     */
    public function persist(AppointmentVisibility $visibility): void;
}