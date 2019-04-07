<?php

namespace App\Repository;

use App\Entity\AppointmentCategory;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentCategoryRepository implements AppointmentCategoryRepositoryInterface {
    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

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
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @param AppointmentCategory $appointmentCategory
     */
    public function remove(AppointmentCategory $appointmentCategory): void {
        $this->em->remove($appointmentCategory);
        $this->isTransactionActive || $this->em->flush();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }
}