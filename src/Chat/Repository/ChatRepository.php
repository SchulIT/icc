<?php

namespace App\Chat\Repository;

use App\Chat\Entity\Chat;
use App\Chat\Entity\ChatTag;
use App\Common\Entity\User;
use App\Framework\Repository\AbstractRepository;
use App\Chat\Repository\ChatRepositoryInterface;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;

class ChatRepository extends AbstractRepository implements ChatRepositoryInterface {

    public function findAllByUserPaginated(PaginationQuery $paginationQuery, User $user, bool $archived, ChatTag|null $tag = null): PaginatedResult {
        $qb = $this->em->createQueryBuilder();
        $qbInner = $this->em->createQueryBuilder();

        $qbInner->select('cInner.id')
            ->from(Chat::class, 'cInner')
            ->leftJoin('cInner.participants', 'pInner')
            ->leftJoin('cInner.userTags', 'tagInner')
            ->where('pInner = :user');

        if($tag !== null) {
            $qbInner
                ->andWhere('tagInner.tag = :tag')
                ->andWhere('tagInner.user = :user');
            $qb->setParameter('tag', $tag);
            $qb->setParameter('user', $user);
        }

        $qb->select(['c', 'p'])
            ->from(Chat::class, 'c')
            ->leftJoin('c.participants', 'p')
            ->leftJoin('c.messages', 'm')
            ->where(
                $qb->expr()->in('c.id', $qbInner->getDQL())
            )
            ->andWhere('c.isArchived = :isArchived')
            ->setParameter('isArchived', $archived)
            ->addOrderBy('m.createdAt', 'desc')
            ->setParameter('user', $user->getId());

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
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

    public function archiveAll(): int {
        return $this->em->createQueryBuilder()
            ->update(Chat::class, 'c')
            ->set('c.isArchived', true)
            ->getQuery()
            ->execute();
    }
}
