<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Sorting\StudentGroupMembershipStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class StudentRepository implements StudentRepositoryInterface {
    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    private function getDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['s', 'g'])
            ->from(Student::class, 's')
            ->leftJoin('s.grade', 'g');
    }

    /**
     * @param int $id
     * @return Student|null
     */
    public function findOneById(int $id): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $externalId
     * @return Student|null
     */
    public function findOneByExternalId(string $externalId): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade): array {
        return $this->getDefaultQueryBuilder()
            ->andWhere('g.id = :gradeId')
            ->setParameter('gradeId', $grade->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByQuery(string $query): array {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(Student::class, 'sInner')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('sInner.firstname', ':query'),
                    $qb->expr()->like('sInner.lastname', ':query')
                )
            )
            ->setParameter('query', '%' . $query . '%');

        $qb
            ->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('query', $query);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Student[]
     */
    public function findAll() {
        return $this->getDefaultQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Student $student
     */
    public function persist(Student $student): void {
        $this->em->persist($student);
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @param Student $student
     */
    public function remove(Student $student): void {
        $this->em->remove($student);
        $this->isTransactionActive || $this->em->flush();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(Student::class, 'sInner')
            ->where($qb->expr()->in('sInner.externalId', ':externalIds'));

        $qb
            ->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }
}