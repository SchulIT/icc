<?php

namespace App\LearningManagementSystem\Repository;

use App\Framework\Repository\AbstractTransactionalRepository;
use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\LearningManagementSystem\Repository\LearningManagementSystemRepositoryInterface;

class LearningManagementSystemRepository extends AbstractTransactionalRepository implements LearningManagementSystemRepositoryInterface {

    public function findOneById(int $id): ?LearningManagementSystem {
        return $this->em->getRepository(LearningManagementSystem::class)
            ->findOneBy(['id' => $id]);
    }

    public function findAll(): array {
        return $this->em->getRepository(LearningManagementSystem::class)
            ->findBy([], ['name' => 'asc']);
    }

    public function persist(LearningManagementSystem $lms): void {
        $this->em->persist($lms);
        $this->flushIfNotInTransaction();
    }

    public function remove(LearningManagementSystem $lms): void {
        $this->em->remove($lms);
        $this->flushIfNotInTransaction();
    }
}