<?php

namespace App\Entity;

use App\Repository\AbstractRepository;
use App\Repository\AppointmentVisibilityRepositoryInterface;

class AppointmentVisibilityRepository extends AbstractRepository implements AppointmentVisibilityRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(AppointmentVisibility::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(AppointmentVisibility $visibility): void {
        $this->em->persist($visibility);
        $this->em->flush();
    }
}