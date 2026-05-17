<?php

namespace App\Document\Repository;

use App\Common\Entity\Grade;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Document\Entity\Document;
use App\Document\Entity\DocumentCategory;
use App\Framework\Repository\AbstractRepository;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
use Doctrine\ORM\QueryBuilder;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['d', 'a', 'c', 'g', 'v'])
            ->from(Document::class, 'd')
            ->leftJoin('d.authors', 'a')
            ->leftJoin('d.category', 'c')
            ->leftJoin('d.grades', 'g')
            ->leftJoin('d.visibilities', 'v');
    }

    public function findOneById(int $id): ?Document {
        $qb = $this->createDefaultQueryBuilder();
        $qb->where('d.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByUuid(string $uuid): ?Document {
        $qb = $this->createDefaultQueryBuilder();
        $qb->where('d.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function countDocumentsEditableByAuthor(User $user): int {
         $qb = $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT d.id)')
            ->from(Document::class, 'd')
            ->leftJoin('d.authors', 'a')
            ->where('a.id = :user')
            ->setParameter('user', $user->getId());
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Document[]
     */
    public function findAll(): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb->orderBy('d.title', 'asc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllFor(UserType $type, ?Grade $grade = null, ?string $q = null): array {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('dInner.id')
            ->from(Document::class, 'dInner')
            ->leftJoin('dInner.grades', 'gInner')
            ->leftJoin('dInner.visibilities', 'vInner')
            ->where('vInner.userType = :type');

        $qb->setParameter('type', $type->value);

        if($grade !== null) {
            $qbInner->andWhere('gInner.id = :grade');
            $qb->setParameter('grade', $grade->getId());
        }

        if($q !== null) {
            $qbInner->andWhere(
                $qb->expr()->orX(
                    'MATCH (dInner.title) AGAINST(:q) > 0',
                    'MATCH (dInner.content) AGAINST(:q) > 0'
                )
            );
            $qb->setParameter('q', $q);
        }

        $qb->where(
            $qb->expr()->in('d.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    public function findPaginated(PaginationQuery $paginationQuery, ?UserType $userType, ?Grade $grade = null, ?DocumentCategory $category = null, ?string $query = null): PaginatedResult {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('dInner.id')
            ->from(Document::class, 'dInner')
            ->leftJoin('dInner.visibilities', 'vInner')
            ->leftJoin('dInner.category', 'cInner')
            ->leftJoin('dInner.grades', 'gInner');

        if($userType !== null) {
            $qbInner->andWhere('vInner.userType = :userType');
            $qb->setParameter('userType', $userType->value);
        }

        if($grade !== null) {
            $qbInner->andWhere('gInner.id = :grade');
            $qb->setParameter('grade', $grade->getId());
        }

        if($category !== null) {
            $qbInner->andWhere('cInner.id = :category');
            $qb->setParameter('category', $category->getId());
        }

        if(!empty($query)) {
            $qbInner->andWhere(
                $qb->expr()->orX(
                    'MATCH (dInner.title) AGAINST(:q) > 0',
                    'MATCH (dInner.content) AGAINST(:q) > 0'
                )
            );
            $qb->setParameter('q', $query);
        }

        $qb
            ->andWhere(
                $qb->expr()->in('d.id', $qbInner->getDQL())
            )
            ->orderBy('d.title', 'asc');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }

    public function persist(Document $document): void {
        /*
         * Ensure that all 1:N relations are set correctly
         * (VichUploader does not seem to use add*() methods)
         */
        foreach($document->getAttachments() as $attachment) {
            $attachment->setDocument($document);
        }

        $this->em->persist($document);
        $this->em->flush();
    }

    public function remove(Document $document): void {
        $this->em->remove($document);
        $this->em->flush();
    }

}