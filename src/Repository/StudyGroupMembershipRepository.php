<?php

namespace App\Repository;

use App\Entity\StudyGroupMembership;
use Doctrine\ORM\EntityManagerInterface;

class StudyGroupMembershipRepository implements StudyGroupMembershipRepositoryInterface {

    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(StudyGroupMembership::class)
            ->findAll();
    }

    public function persist(StudyGroupMembership $membership): void {
        $this->em->persist($membership);
        $this->isTransactionActive || $this->em->flush();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete()
            ->from(StudyGroupMembership::class, 'm')
            ->getQuery()
            ->execute();
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