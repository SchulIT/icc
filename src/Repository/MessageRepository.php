<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\MessageScope;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;

class MessageRepository implements MessageRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Message|null
     */
    public function findOneById(int $id): ?Message {
        return $this->em->getRepository(Message::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findBy(MessageScope $scope, UserType $userType, \DateTime $today = null, array $studyGroups = []) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(Message::class, 'mInner')
            ->leftJoin('mInner.visibilities', 'vInner')
            ->where('vInner.userType = :userType')
            ->andWhere('mInner.scope = :scope');

        if($today !== null) {
            $qbInner
                ->andWhere('mInner.startDate <= :today')
                ->andWhere('mInner.expireDate >= :today');

            $qb->setParameter('today', $today);
        }

        if(count($studyGroups) > 0) {
            $qbInner
                ->leftJoin('mInner.studyGroups', 'sgInner')
                ->andWhere($qb->expr()->in('sgInner.id', ':studyGroups'));

            $qb->setParameter('studyGroups', array_map(function(StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }, $studyGroups));
        }

        $qb
            ->select(['m', 'sg'])
            ->from(Message::class, 'm')
            ->leftJoin('m.attachments', 'a')
            ->leftJoin('m.createdBy', 'c')
            ->leftJoin('m.files', 'f')
            ->leftJoin('m.studyGroups', 'sg')
            ->leftJoin('m.visibilities', 'v')
            ->where($qb->expr()->in('m.id', $qbInner->getDQL()))
            ->setParameter('scope', $scope)
            ->setParameter('userType', $userType);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Message[]
     */
    public function findAll() {
        return $this->em->getRepository(Message::class)
            ->findAll();
    }

    /**
     * @param Message $message
     */
    public function persist(Message $message): void {
        $this->em->persist($message);
        $this->em->flush();
    }

    /**
     * @param Message $message
     */
    public function remove(Message $message): void {
        $this->em->remove($message);
        $this->em->flush();
    }
}