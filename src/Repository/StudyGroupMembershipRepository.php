<?php

namespace App\Repository;

use App\Entity\StudyGroupMembership;

class StudyGroupMembershipRepository extends AbstractTransactionalRepository implements StudyGroupMembershipRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(StudyGroupMembership::class)
            ->findAll();
    }

    public function persist(StudyGroupMembership $membership): void {
        $this->em->persist($membership);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete()
            ->from(StudyGroupMembership::class, 'm')
            ->getQuery()
            ->execute();
    }
}