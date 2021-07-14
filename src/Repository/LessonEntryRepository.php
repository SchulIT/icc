<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\LessonEntry;
use App\Entity\Tuition;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class LessonEntryRepository extends AbstractRepository implements LessonEntryRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em
            ->createQueryBuilder()
            ->select(['e', 't', 'tt'])
            ->from(LessonEntry::class, 'e')
            ->leftJoin('e.teacher', 't')
            ->leftJoin('e.tuition', 'tt');
    }

    private function applyStartEnd(QueryBuilder $qb, DateTime $start, DateTime $end): QueryBuilder {
        return $qb
            ->leftJoin('e.lesson', 'l')
            ->andWhere('l.date >= :start')
            ->andWhere('l.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuition(Tuition $tuition, DateTime $start, DateTime $end): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);

        $qb->andWhere('tt.id = :tuition')
            ->setParameter('tuition', $tuition->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade, DateTime $start, DateTime $end): array {
        $qb = $this->createDefaultQueryBuilder();
        $qb = $this->applyStartEnd($qb, $start, $end);

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.studyGroup', 'sInner')
            ->leftJoin('sInner.grades', 'gInner')
            ->where('gInner.id = :grade');

        $qb->andWhere(
            $qb->expr()->in('tt.id', $qbInner->getDQL())
        )
            ->setParameter('grade', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    public function persist(LessonEntry $entry): void {
        $this->em->persist($entry);
        $this->em->flush();
    }

    public function remove(LessonEntry $entry): void {
        $this->em->remove($entry);
        $this->em->flush();
    }
}