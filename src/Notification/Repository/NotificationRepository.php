<?php

namespace App\Notification\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Exam\Entity\Exam;
use App\Notification\Entity\Notification;
use App\Common\Entity\User;
use App\Notification\Repository\NotificationRepositoryInterface;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

class NotificationRepository extends AbstractRepository implements NotificationRepositoryInterface {

    public function countUnreadForUser(User $user): int {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(n.id)')
            ->from(Notification::class, 'n')
            ->where('n.recipient = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user->getId());

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findUnreadForUser(User $user): array {
        $qb = $this->em->createQueryBuilder()
            ->select('n')
            ->from(Notification::class, 'n')
            ->where('n.recipient = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user->getId())
            ->orderBy('n.createdAt', 'desc');

        return $qb->getQuery()->getResult();
    }

    public function getUserPaginator(User $user, int $itemsPerPage, int &$page): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('n')
            ->from(Notification::class, 'n')
            ->where('n.recipient = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('n.createdAt', 'desc');

        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;
        $paginator = new Paginator($qb);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    public function markAllReadForUser(User $user): int {
        return $this->em->createQueryBuilder()
            ->update(Notification::class, 'n')
            ->set('n.isRead', true)
            ->where('n.recipient = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllReadForUserAndLink(User $user, string $link): int {
        return $this->em->createQueryBuilder()
            ->update(Notification::class, 'n')
            ->set('n.isRead', true)
            ->where('n.recipient = :user')
            ->andWhere('n.link = :link')
            ->setParameter('user', $user->getId())
            ->setParameter('link', $link)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUnreadForUserAndLink(User $user, string $link): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(Notification::class, 'n')
            ->where('n.recipient = :user')
            ->andWhere('n.link = :link')
            ->setParameter('user', $user->getId())
            ->setParameter('link', $link)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function persist(Notification $notification): void {
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function remove(Notification $notification): void {
        $this->em->remove($notification);
        $this->em->flush();
    }

    public function removeAll(): int {
        return $this->em->createQueryBuilder()
            ->delete(Notification::class, 'n')
            ->getQuery()
            ->execute();
    }

    public function removeBetween(DateTime $start, DateTime $end): int {
        return $this->em->createQueryBuilder()
            ->delete(Notification::class, 'n')
            ->where('n.createdAt >= :start')
            ->andWhere('n.createdAt <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
    }
}