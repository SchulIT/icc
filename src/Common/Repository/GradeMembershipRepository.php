<?php

namespace App\Common\Repository;

use App\Framework\Repository\AbstractTransactionalRepository;
use App\Common\Entity\GradeMembership;
use App\Common\Entity\Section;
use App\Common\Repository\GradeMembershipRepositoryInterface;

class GradeMembershipRepository extends AbstractTransactionalRepository implements GradeMembershipRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAllBySection(Section $section): array {
        return $this->em->getRepository(GradeMembership::class)
            ->findBy([
                'section' => $section
            ]);
    }

    public function persist(GradeMembership $gradeMembership): void {
        $this->em->persist($gradeMembership);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(Section $section): void {
        $qb = $this->em->createQueryBuilder()
            ->delete()
            ->from(GradeMembership::class, 'm');

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(GradeMembership::class, 'mInner')
            ->leftJoin('mInner.section', 'sInner')
            ->where('sInner.id = :section');

        $qb->where(
            $qb->expr()->in('m.id', $qbInner->getDQL())
        )
            ->setParameter('section', $section->getId());

        $qb->getQuery()->execute();
    }
}