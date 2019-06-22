<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;

class TimetableSupervisionRepository extends AbstractTransactionalRepository implements TimetableSupervisionRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?TimetableSupervision {
        return $this->em->getRepository(TimetableSupervision::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriodAndTeacher(TimetablePeriod $period, Teacher $teacher) {
        return $this->em->getRepository(TimetableSupervision::class)
            ->findBy([
                'period' => $period,
                'teacher' => $teacher
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(TimetableSupervision::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetableSupervision $supervision): void {
        $this->em->persist($supervision);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function remove(TimetableSupervision $supervision): void {
        $this->em->remove($supervision);
        $this->flushIfNotInTransaction();
    }
}