<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Tuition;
use DateTime;

class LessonRepository extends AbstractTransactionalRepository implements LessonRepositoryInterface {

    public function countByDate(DateTime $start, DateTime $end): int {
        return $this->em
            ->createQueryBuilder()
            ->select('COUNT(l.id)')
            ->from(Lesson::class, 'l')
            ->where('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $dateTime): array {
        return $this->em->getRepository(Lesson::class)
            ->findBy([
                'date' => $dateTime
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuitions(array $tuitions, DateTime $start, DateTime $end): array {
        $ids = array_map(function(Tuition $tuition) {
            return $tuition->getId();
        }, $tuitions);

        $qb = $this->em->createQueryBuilder();

        $qb->select(['l'])
            ->from(Lesson::class, 'l')
            ->leftJoin('l.tuition', 't')
            ->where(
                $qb->expr()->in('t.id', ':tuitions')
            )
            ->andWhere('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('tuitions', $ids);

        return $qb->getQuery()->getResult();
    }

    public function persist(Lesson $lesson): void {
        $this->em->persist($lesson);
        $this->flushIfNotInTransaction();
    }

    public function remove(Lesson $lesson): void {
        $this->em->remove($lesson);
        $this->flushIfNotInTransaction();
    }
}