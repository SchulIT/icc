<?php

namespace App\Repository;

use App\Entity\AppointmentCategory;

class AppointmentCategoryRepository extends AbstractTransactionalRepository implements AppointmentCategoryRepositoryInterface {

    public function findOneById(int $id): ?AppointmentCategory {
        return $this->em->getRepository(AppointmentCategory::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    public function findOneByExternalId(string $externalId): ?AppointmentCategory {
        return $this->em->getRepository(AppointmentCategory::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(AppointmentCategory::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(AppointmentCategory $appointmentCategory): void {
        $this->em->persist($appointmentCategory);
        $this->flushIfNotInTransaction();
    }

    public function remove(AppointmentCategory $appointmentCategory): void {
        $this->em->remove($appointmentCategory);
        $this->flushIfNotInTransaction();
    }
}