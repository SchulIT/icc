<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;

class ChatRepository extends AbstractRepository implements ChatRepositoryInterface {

    public function findAllByUser(User $user): array {
        $qb = $this->em->createQueryBuilder();
        $qbInner = $this->em->createQueryBuilder();

        $qbInner->select('cInner.id')
            ->from(Chat::class, 'cInner')
            ->leftJoin('cInner.participants', 'pInner')
            ->where('pInner = :user');

        return $qb->select(['c', 'p'])
            ->from(Chat::class, 'c')
            ->leftJoin('c.participants', 'p')
            ->leftJoin('c.messages', 'm')
            ->where(
                $qb->expr()->in('c.id', $qbInner->getDQL())
            )
            ->addOrderBy('m.createdAt', 'desc')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(Chat $chat): void {
        $this->em->persist($chat);
        $this->em->flush();
    }

    public function remove(Chat $chat): void {
        $this->em->remove($chat);
        $this->em->flush();
    }

    public function removeAll(): int {
        return $this->em->createQueryBuilder()
            ->delete(Chat::class, 'c')
            ->getQuery()
            ->execute();
    }
}