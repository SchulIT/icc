<?php

namespace App\Repository;

use App\Entity\AppointmentVisibility;

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