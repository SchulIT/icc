<?php

namespace App\Repository;

use App\Entity\AttendanceFlag;
use App\Entity\Subject;

class LessonAttendanceFlagRepository extends AbstractRepository implements LessonAttendanceFlagRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(AttendanceFlag::class)
            ->findBy([], ['description' => 'asc']);
    }

    public function findAllBySubject(Subject $subject): array {
        $qb = $this->em->createQueryBuilder();

        return $qb->select('f')
            ->from(AttendanceFlag::class, 'f')
            ->leftJoin('f.subjects', 's')
            ->where($qb->expr()->isNull('s.id'))
            ->orWhere('s.id = :subject')
            ->orderBy('f.description', 'asc')
            ->setParameter('subject', $subject->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(AttendanceFlag $flag): void {
        $this->em->persist($flag);
        $this->em->flush();
    }

    public function remove(AttendanceFlag $flag): void {
        $this->em->remove($flag);
        $this->em->flush();
    }
}