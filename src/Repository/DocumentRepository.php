<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\QueryBuilder;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        $qb = $this->em->createQueryBuilder()
            ->select(['d', 'att', 'a', 'c', 'sg', 'v'])
            ->from(Document::class, 'd')
            ->leftJoin('d.attachments', 'att')
            ->leftJoin('d.authors', 'a')
            ->leftJoin('d.category', 'c')
            ->leftJoin('d.studyGroups', 'sg')
            ->leftJoin('d.visibilities', 'v');

        return $qb;
    }

    /**
     * @param int $id
     * @return Document|null
     */
    public function findOneById(int $id): ?Document {
        $qb = $this->createDefaultQueryBuilder();
        $qb->where('d.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param DocumentCategory $category
     * @return Document[]
     */
    public function findAllByCategory(DocumentCategory $category) {
        $qb = $this->createDefaultQueryBuilder();
        $qb->where('c.id = :id')
            ->setParameter('id', $category->getId())
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByAuthor(User $user): array {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('dInner.id')
            ->from(Document::class, 'dInner')
            ->leftJoin('dInner.authors', 'aInner')
            ->where('aInner.id = :user');

        $qb->where(
            $qb->expr()->in('d.id', $qbInner->getDQL())
        );

        $qb->setParameter('user', $user->getId());
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Document[]
     */
    public function findAll() {
        $qb = $this->createDefaultQueryBuilder();
        $qb->orderBy('d.title', 'asc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllFor(UserType $type, ?StudyGroup $studyGroup = null, ?string $q = null) {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('dInner.id')
            ->from(Document::class, 'dInner')
            ->leftJoin('dInner.studyGroups', 'sgInner')
            ->leftJoin('dInner.visibilities', 'vInner')
            ->where('vInner.userType = :type');

        $qb->setParameter('type', $type->getValue());

        if($studyGroup !== null) {
            $qbInner->andWhere('sgInner.id = :group');
            $qb->setParameter('group', $studyGroup->getId());
        }

        if($q !== null) {
            $qbInner->andWhere('MATCH (d.content) AGAINST(:q) > 0');
            $qb->setParameter('q', $q);
        }

        $qb->where(
            $qb->expr()->in('d.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Document $document
     */
    public function persist(Document $document): void {
        $this->em->persist($document);
        $this->em->flush();
    }

    /**
     * @param Document $document
     */
    public function remove(Document $document): void {
        $this->em->remove($document);
        $this->em->flush();
    }

}