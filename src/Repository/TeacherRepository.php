<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;

class TeacherRepository implements TeacherRepositoryInterface {

    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Teacher {
        return $this->em->getRepository(Teacher::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByAcronym(string $acronym): ?Teacher {
        return $this->em->getRepository(Teacher::class)
            ->findOneBy([
                'acronym' => $acronym
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByAcronym(array $acronyms): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('t')
            ->from(Teacher::class, 't')
            ->where($qb->expr()->in('t.acronym', ':acronyms'))
            ->setParameter('acronyms', $acronyms);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(Teacher::class)
            ->findBy([], [
                'acronym' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(Teacher $teacher): void {
        $this->em->persist($teacher);
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(Teacher $teacher): void {
        $this->em->remove($teacher);
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