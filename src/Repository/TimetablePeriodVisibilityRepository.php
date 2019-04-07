<?php

namespace App\Repository;

use App\Entity\TimetablePeriodVisibility;
use Doctrine\ORM\EntityManagerInterface;

class TimetablePeriodVisibilityRepository implements TimetablePeriodVisibilityRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(TimetablePeriodVisibility::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetablePeriodVisibility $periodVisibility): void {
        $this->em->persist($periodVisibility);
        $this->em->flush();
    }
}