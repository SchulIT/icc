<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Entity\TimetableSupervision;
use DateTime;

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
    public function findAllByTeacher(DateTime $startDate, DateTime $endDate, Teacher $teacher): array {
        return $this->em->createQueryBuilder()
            ->select(['s', 't'])
            ->from(TimetableSupervision::class, 's')
            ->leftJoin('s.teacher', 't')
            ->where('t.id = :teacher')
            ->andWhere('s.date >= :startDate')
            ->andWhere('s.date <= :endDate')
            ->setParameter('teacher', $teacher->getId())
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function findAllByRange(DateTime $startDate, DateTime $endDate): array {
        return $this->em->createQueryBuilder()
            ->select(['s', 't'])
            ->from(TimetableSupervision::class, 's')
            ->leftJoin('s.teacher', 't')
            ->andWhere('s.date >= :startDate')
            ->andWhere('s.date <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
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

    public function removeBetween(DateTime $start, DateTime $end): int {
        return $this->em->createQueryBuilder()
            ->delete(TimetableSupervision::class, 's')
            ->where('s.date >= :start')
            ->andWhere('s.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
    }


}