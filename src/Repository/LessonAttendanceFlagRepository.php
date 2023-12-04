<?php

namespace App\Repository;

use App\Entity\LessonAttendanceFlag;
use App\Entity\Subject;

class LessonAttendanceFlagRepository extends AbstractRepository implements LessonAttendanceFlagRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(LessonAttendanceFlag::class)
            ->findBy([], ['description' => 'asc']);
    }

    public function findAllBySubject(Subject $subject): array {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('f')
            ->from(LessonAttendanceFlag::class, 'f')
            ->leftJoin('f.subjects', 's')
            ->where($qb->expr()->isNull('s.id'))
            ->orWhere('s.id = :subject')
            ->orderBy('f.description', 'asc')
            ->setParameter('subject', $subject->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(LessonAttendanceFlag $flag): void {
        $this->em->persist($flag);
        $this->em->flush();
    }

    public function remove(LessonAttendanceFlag $flag): void {
        $this->em->remove($flag);
        $this->em->flush();
    }
}