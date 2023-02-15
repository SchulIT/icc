<?php

namespace App\Repository;

use App\Entity\LearningManagementSystem;

class LearningManagementSystemRepository extends AbstractTransactionalRepository implements LearningManagementSystemRepositoryInterface {

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