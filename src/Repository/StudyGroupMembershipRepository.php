<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\StudyGroup;
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

    public function removeAll(Section $section): void {
        $qb = $this->em->createQueryBuilder()
            ->delete()
            ->from(StudyGroupMembership::class, 'm');

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(StudyGroupMembership::class, 'mInner')
            ->leftJoin('mInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.section', 'sInner')
            ->where('sInner.id = :section');

        $qb->where(
            $qb->expr()->in('m.id', $qbInner->getDQL())
        )
            ->setParameter('section', $section->getId());

        $qb->getQuery()->execute();
    }
}