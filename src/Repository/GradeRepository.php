<?php

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\ORM\EntityManagerInterface;

class GradeRepository implements GradeRepositoryInterface {

    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Grade|null
     */
    public function findOneById(int $id): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?Grade {
        return $this->em->getRepository(Grade::class)
            ->findOneBy([
                'name' => $name
            ]);
    }

    /**
     * @return Grade[]
     */
    public function findAll() {
        return $this->em->getRepository(Grade::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('s')
            ->from(Grade::class, 'g')
            ->where($qb->expr()->in('g.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Grade $grade
     */
    public function persist(Grade $grade): void {
        $this->em->persist($grade);
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @param Grade $grade
     */
    public function remove(Grade $grade): void {
        $this->em->remove($grade);
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
}