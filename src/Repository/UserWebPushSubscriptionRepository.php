<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserWebPushSubscription;

class UserWebPushSubscriptionRepository extends AbstractRepository implements UserWebPushSubscriptionRepositoryInterface {

    public function findAllForUsers(array $users): array {
        $userIds = array_map(function(User $user) {
            return $user->getId();
        }, $users);

        $qb = $this->em->createQueryBuilder()
            ->select(['s', 'u'])
            ->from(UserWebPushSubscription::class, 's')
            ->leftJoin('s.user', 'u');

        $qb->where(
            $qb->expr()->in('u.id', ':ids')
        )
            ->setParameter('ids', $userIds);

        return $qb->getQuery()->getResult();
    }

    public function findAllForExam(): array {
        $qb = $this->em->createQueryBuilder()
            ->select(['s', 'u'])
            ->from(UserWebPushSubscription::class, 's')
            ->leftJoin('s.user', 'u')
            ->where('u.isExamNotificationsEnabled = true');

        return $qb->getQuery()->getResult();
    }

    public function findAllForSubstitutions(): array {
        $qb = $this->em->createQueryBuilder()
            ->select(['s', 'u'])
            ->from(UserWebPushSubscription::class, 's')
            ->leftJoin('s.user', 'u')
            ->where('u.isSubstitutionNotificationsEnabled = true');

        return $qb->getQuery()->getResult();
    }

    public function persist(UserWebPushSubscription $subscription) {
        $this->em->persist($subscription);
        $this->em->flush();
    }

    public function remove(UserWebPushSubscription $subscription) {
        $this->em->remove($subscription);
        $this->em->flush();
    }
}