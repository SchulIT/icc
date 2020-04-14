<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\MessageScope;
use App\Entity\StudyGroup;
use App\Entity\UserType;
use Doctrine\ORM\QueryBuilder;

class MessageRepository extends AbstractRepository implements MessageRepositoryInterface {

    /**
     * @param int $id
     * @return Message|null
     */
    public function findOneById(int $id): ?Message {
        return $this->em->createQueryBuilder()
            ->select(['m', 'sg', 'sgg', 'v', 'f', 'c', 'a'])
            ->from(Message::class, 'm')
            ->leftJoin('m.attachments', 'a')
            ->leftJoin('m.createdBy', 'c')
            ->leftJoin('m.files', 'f')
            ->leftJoin('m.studyGroups', 'sg')
            ->leftJoin('sg.grades', 'sgg')
            ->leftJoin('m.visibilities', 'v')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

    }

    private function getFindByQueryBuilder(MessageScope $scope, UserType $userType, \DateTime $today = null, array $studyGroups = [], bool $archive = false): QueryBuilder {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(Message::class, 'mInner')
            ->leftJoin('mInner.visibilities', 'vInner')
            ->where('vInner.userType = :userType')
            ->andWhere('mInner.scope = :scope');

        if($today !== null) {
            if($archive === true) {
                $qbInner
                    ->andWhere('mInner.expireDate < :today');
                $qb->setParameter('today', $today);
            } else {
                $qbInner
                    ->andWhere('mInner.startDate <= :today')
                    ->andWhere('mInner.expireDate >= :today');

                $qb->setParameter('today', $today);
            }
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
            ->where($qb->expr()->in('m.id', $qbInner->getDQL()))
            ->setParameter('scope', $scope->getValue())
            ->setParameter('userType', $userType->getValue());

        return $qb;
    }

    /**
     * @inheritDoc
     */
    public function findBy(MessageScope $scope, UserType $userType, \DateTime $today = null, array $studyGroups = [], bool $archive = false) {
        return $this->getFindByQueryBuilder($scope, $userType, $today, $studyGroups, $archive)->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function countBy(MessageScope $scope, UserType $userType, \DateTime $today = null, array $studyGroups = [], bool $archive = false): int {
        $qb = $this->getFindByQueryBuilder($scope, $userType, $today, $studyGroups, $archive);

        $qb->select('COUNT(DISTINCT m.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Message[]
     */
    public function findAll() {
        return $this->em->getRepository(Message::class)
            ->findAll();
    }

    /**
     * @param UserType $userType
     * @return Message[]
     */
    public function findAllByUserType(UserType $userType) {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(Message::class, 'mInner')
            ->leftJoin('mInner.visibilities', 'vInner')
            ->where('vInner.userType = :userType');

        $qb
            ->where($qb->expr()->in('m.id', $qbInner->getDQL()))
            ->setParameter('userType', $userType->getValue());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Message $message
     */
    public function persist(Message $message): void {
        /*
         * Ensure that all 1:N relations are set correctly
         * (VichUploader does not seem to use add*() methods)
         */
        foreach($message->getAttachments() as $attachment) {
            $attachment->setMessage($message);
        }

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

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['m', 'v', 'c'])
            ->from(Message::class, 'm')
            ->leftJoin('m.createdBy', 'c')
            ->leftJoin('m.visibilities', 'v');
    }

    /**
     * @inheritDoc
     */
    public function removeMessageFile(MessageFile $file): void {
        $this->em->remove($file);
        $this->em->flush();
    }

}