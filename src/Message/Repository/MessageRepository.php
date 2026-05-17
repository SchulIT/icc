<?php

namespace App\Message\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Common\Entity\Grade;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
use App\Message\Entity\Message;
use App\Message\Entity\MessageFile;
use App\Message\Entity\MessageScope;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Message\Repository\MessageRepositoryInterface;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;

class MessageRepository extends AbstractRepository implements MessageRepositoryInterface {

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

    private function getFindByQueryBuilder(MessageScope|null $scope, UserType|null $userType, DateTime|null $today = null, array $studyGroups = [], ?string $query = null, User|null $author = null): QueryBuilder {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(Message::class, 'mInner')
            ->leftJoin('mInner.visibilities', 'vInner');

        if($scope !== null) {
            $qbInner->andWhere('mInner.scope = :scope');
            $qb->setParameter('scope', $scope);
        }

        if($userType !== null) {
            $qbInner->andWhere('vInner.userType IN(:userTypes)');

            if($userType === UserType::Parent) {
                $qb->setParameter('userTypes', [ $userType->value, UserType::Student->value ]);
            } else {
                $qb->setParameter('userTypes', [$userType->value]);
            }
        }

        if($today !== null) {
            $qbInner
                ->andWhere('mInner.startDate <= :today')
                ->andWhere('mInner.expireDate >= :today');

            $qb->setParameter('today', $today);
        }

        if($author !== null) {
            $qbInner->andWhere('mInner.createdBy = :author');
            $qb->setParameter('author', $author->getId());
        }

        if($query !== null) {
            $qbInner->andWhere(
                $qb->expr()->orX(
                    'MATCH (mInner.title) AGAINST(:q) > 0',
                    'MATCH (mInner.content) AGAINST(:q) > 0'
                )
            );
            $qb->setParameter('q', $query);
        }

        if(count($studyGroups) > 0) {
            $qbInner
                ->leftJoin('mInner.studyGroups', 'sgInner')
                ->andWhere($qb->expr()->in('sgInner.id', ':studyGroups'));

            $qb->setParameter('studyGroups', array_map(fn(StudyGroup $studyGroup) => $studyGroup->getId(), $studyGroups));
        }

        $qb
            ->where($qb->expr()->in('m.id', $qbInner->getDQL()));

        return $qb;
    }

    /**
     * @inheritDoc
     */
    public function findBy(MessageScope $scope, UserType $userType, DateTime|null $today = null, array $studyGroups = []): array {
        return $this->getFindByQueryBuilder($scope, $userType, $today, $studyGroups)->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function countBy(MessageScope $scope, UserType $userType, DateTime|null $today = null, array $studyGroups = []): int {
        $qb = $this->getFindByQueryBuilder($scope, $userType, $today, $studyGroups);

        $qb->select('COUNT(DISTINCT m.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Message[]
     */
    public function findAll(): array {
        return $this->em->getRepository(Message::class)
            ->findAll();
    }

    /**
     * @return Message[]
     */
    public function findAllByUserType(UserType $userType, ?User $author = null): array {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(Message::class, 'mInner')
            ->leftJoin('mInner.visibilities', 'vInner')
            ->where('vInner.userType = :userType');

        $qb
            ->where($qb->expr()->in('m.id', $qbInner->getDQL()))
            ->setParameter('userType', $userType->value);

        if($author !== null) {
            $qb->andWhere('m.createdBy = :user')
                ->setParameter('user', $author->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade, ?User $author = null): array {
        $qb = $this->createDefaultQueryBuilder();

        $qbStudyGroups = $this->em->createQueryBuilder()
            ->select('sgInnerInner.id')
            ->from(StudyGroup::class, 'sgInnerInner')
            ->leftJoin('sgInnerInner.grades', 'gradesInnerInner')
            ->where('gradesInnerInner.id = :grade');

        $qbInner = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(Message::class, 'mInner')
            ->leftJoin('mInner.studyGroups', 'sgInner')
            ->andWhere($qb->expr()->in('sgInner.id', $qbStudyGroups->getDQL()));

        $qb->setParameter('grade', $grade->getId());

        $qb
            ->where($qb->expr()->in('m.id', $qbInner->getDQL()));

        if($author !== null) {
            $qb->andWhere('m.createdBy = :user')
                ->setParameter('user', $author->getId());
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllByAuthor(User $user): array {
        $qb = $this->createDefaultQueryBuilder();

        $qb->andWhere('m.createdBy = :user')
            ->setParameter('user', $user->getId());

        return $qb->getQuery()->getResult();
    }

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

    /**
     * @inheritDoc
     */
    public function findAllNotificationNotSent(DateTime $dateTime): array {
        $qb = $this->createDefaultQueryBuilder();

        $qb->where(
            $qb->expr()->andX(
                'm.isEmailNotificationSent = false',
                'm.startDate <= :date'
            )
        )
            ->setParameter('date', $dateTime);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getPaginator(int $itemsPerPage, int &$page, MessageScope $scope, UserType $userType, ?DateTime $today = null, array $studyGroups = [], ?string $query = null): Paginator {
        $qb = $this->getFindByQueryBuilder($scope, $userType, $today, $studyGroups, $query)
            ->orderBy('m.expireDate', 'desc');

        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;
        $paginator = new Paginator($qb);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    public function findPaginated(PaginationQuery $paginationQuery, MessageScope|null $scope = null, UserType|null $userType = null, ?DateTime $today = null, array $studyGroups = [ ], ?string $query = null, User|null $author = null): PaginatedResult {
        $qb = $this->getFindByQueryBuilder($scope, $userType, $today, $studyGroups, $query, $author);
        $qb->orderBy('m.expireDate', 'desc');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }

    private function getExpiredQueryBuilder(DateTime $today): QueryBuilder {
        return $this->createDefaultQueryBuilder()
            ->where('m.expireDate < :today')
            ->setParameter('today', $today);
    }

    public function findExpired(DateTime $today): array {
        return $this->getExpiredQueryBuilder($today)
            ->getQuery()
            ->getResult();
    }

    public function countExpired(DateTime $today): int {
        return $this->getExpiredQueryBuilder($today)
            ->select('COUNT(DISTINCT m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}