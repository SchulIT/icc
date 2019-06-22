<?php

namespace App\Repository;

use App\Entity\Teacher;

class TeacherRepository extends AbstractTransactionalRepository implements TeacherRepositoryInterface {

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
    public function findOneByExternalId(string $externalId): ?Teacher {
        return $this->em->getRepository(Teacher::class)
            ->findOneBy([
                'externalId' => $externalId
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
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function remove(Teacher $teacher): void {
        $this->em->remove($teacher);
        $this->flushIfNotInTransaction();
    }

}