<?php

namespace App\Repository;

use App\Entity\Subject;

class SubjectRepository extends AbstractTransactionalRepository implements SubjectRepositoryInterface {

    /**
     * @param int $id
     * @return Subject|null
     */
    public function findOneById(int $id): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'id'=> $id
            ]);
    }

    /**
     * @param string $abbreviation
     * @return Subject|null
     */
    public function findOneByAbbreviation(string $abbreviation): ?Subject {
        return $this->em->getRepository(Subject::class)
            ->findOneBy([
                'abbreviation' => $abbreviation
            ]);
    }

    /**
     * @return Subject[]
     */
    public function findAll() {
        return $this->em->getRepository(Subject::class)
            ->findAll();
    }

    /**
     * @param Subject $subject
     */
    public function persist(Subject $subject): void {
        $this->em->persist($subject);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Subject $subject
     */
    public function remove(Subject $subject): void {
        $this->em->remove($subject);
        $this->flushIfNotInTransaction();
    }
}