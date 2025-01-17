<?php

namespace App\Repository;

use App\Entity\Checklist;
use App\Entity\User;

class ChecklistRepository extends AbstractRepository implements ChecklistRepositoryInterface {

    public function findAllByUser(User $user): array {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('cInner.id')
            ->from(Checklist::class, 'cInner')
            ->leftJoin('cInner.createdBy', 'uInner')
            ->leftJoin('cInner.sharedWith', 'swInner')
            ->where(
                $qb->expr()->orX(
                    'uInner.id = :user',
                    'swInner.id = :user'
                )
            );

        return $qb
            ->select(['c', 'u'])
            ->from(Checklist::class, 'c')
            ->leftJoin('c.createdBy', 'u')
            ->where(
                $qb->expr()->in('c.id', $qbInner->getDQL())
            )
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(Checklist $checklist): void {
        $this->em->persist($checklist);
        $this->em->flush();
    }

    public function remove(Checklist $checklist): void {
        $this->em->remove($checklist);
        $this->em->flush();
    }
}