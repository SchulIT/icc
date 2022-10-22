<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentCategory;
use App\Entity\Grade;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\QueryBuilder;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        $qb = $this->em->createQueryBuilder()
            ->select(['d', 'a', 'c', 'g', 'v'])
            ->from(Document::class, 'd')
            ->leftJoin('d.authors', 'a')
            ->leftJoin('d.category', 'c')
            ->leftJoin('d.grades', 'g')
            ->leftJoin('d.visibilities', 'v');

        return $qb;
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
    public function findAllFor(UserType $type, ?Grade $grade = null, ?string $q = null) {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('dInner.id')
            ->from(Document::class, 'dInner')
            ->leftJoin('dInner.grades', 'gInner')
            ->leftJoin('dInner.visibilities', 'vInner')
            ->where('vInner.userType = :type');

        $qb->setParameter('type', $type->getValue());

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