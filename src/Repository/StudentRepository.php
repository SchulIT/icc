<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;

class StudentRepository implements StudentRepositoryInterface {
    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Student|null
     */
    public function findOneById(int $id): ?Student {
        return $this->em->getRepository(Student::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return Student|null
     */
    public function findOneByExternalId(string $externalId): ?Student {
        return $this->em->getRepository(Student::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @return Student[]
     */
    public function findAll() {
        return $this->em->getRepository(Student::class)
            ->findAll();
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
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('s')
            ->from(Student::class, 's')
            ->where($qb->expr()->in('s.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }
}