<?php

namespace App\Repository;

use App\Entity\StudentLearningManagementSystemInformation;

class StudentLearningManagementSystemInformationRepository extends AbstractTransactionalRepository implements StudentLearningManagementInformationRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(StudentLearningManagementSystemInformation::class)
            ->findAll();
    }

    public function persist(StudentLearningManagementSystemInformation $information): void {
        $this->em->persist($information);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete(StudentLearningManagementSystemInformation::class)
            ->getQuery()
            ->execute();
    }
}