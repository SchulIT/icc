<?php

namespace App\Repository;

use App\Entity\AppointmentCategory;

class AppointmentCategoryRepository extends AbstractTransactionalRepository implements AppointmentCategoryRepositoryInterface {

    /**
     * @param int $id
     * @return AppointmentCategory|null
     */
    public function findOneById(int $id): ?AppointmentCategory {
        return $this->em->getRepository(AppointmentCategory::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return AppointmentCategory|null
     */
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

    /**
     * @param AppointmentCategory $appointmentCategory
     */
    public function persist(AppointmentCategory $appointmentCategory): void {
        $this->em->persist($appointmentCategory);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param AppointmentCategory $appointmentCategory
     */
    public function remove(AppointmentCategory $appointmentCategory): void {
        $this->em->remove($appointmentCategory);
        $this->flushIfNotInTransaction();
    }
}