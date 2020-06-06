<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Entity\UserWebPushSubscription;

class UserWebPushSubscriptionRepository extends AbstractRepository implements UserWebPushSubscriptionRepositoryInterface {

    public function findAllForMessage(Message $message): array {
        $userIds = [ ];

        $studyGroupsIds = $message->getStudyGroups()->map(function(StudyGroup $studyGroup) {
            return $studyGroup->getId();
        })->toArray();

        /** @var UserTypeEntity $visibility */
        foreach($message->getVisibilities() as $visibility) {
            if($visibility->getUserType()->equals(UserType::Student()) || $visibility->getUserType()->equals(UserType::Parent())) {
                $qbInner = $this->em->createQueryBuilder()
                    ->select('sInner.id')
                    ->from(StudyGroup::class, 'sgInner')
                    ->leftJoin('sgInner.memberships', 'mInner')
                    ->leftJoin('mInner.student', 'sInner')
                    ->where('sgInner.id IN (:studyGroups)');

                $qb = $this->em->createQueryBuilder();
                $qb
                    ->select('u.id')
                    ->from(User::class, 'u')
                    ->leftJoin('u.students', 's')
                    ->where($qb->expr()->in('s.id', $qbInner->getDQL()))
                    ->setParameter('studyGroups', $studyGroupsIds);
            } else {
                $qb = $this->em->createQueryBuilder()
                    ->select('u.id')
                    ->from(User::class, 'u')
                    ->where('u.userType = :type')
                    ->setParameter('type', $visibility->getUserType());
            }

            $result = $qb->getQuery()->getScalarResult();
            $userIds = array_merge($userIds, array_column($result, 'id'));
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(['s', 'u'])
            ->from(UserWebPushSubscription::class, 's')
            ->leftJoin('s.user', 'u')
            ->where('u.isMessageNotificationsEnabled = true')
            ->andWhere($qb->expr()->in('u.id', ':userIds'))
            ->setParameter('userIds', $userIds);

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