<?php

namespace App\Repository;

use App\Entity\Absence;
use App\Entity\Student;

class AbsenceRepository extends AbstractTransactionalRepository implements AbsenceRepositoryInterface {

    public function findAll(): array {
        return $this->em
            ->getRepository(Absence::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findAllTeachers(\DateTime $date): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 't'])
            ->from(Absence::class, 'p')
            ->leftJoin('p.teacher', 't')
            ->where($qb->expr()->isNotNull('t.id'))
            ->andWhere('p.date = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllStudyGroups(\DateTime $date): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 't'])
            ->from(Absence::class, 'p')
            ->leftJoin('p.studyGroup', 'sg')
            ->where($qb->expr()->isNotNull('sg.id'))
            ->andWhere('p.date = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllStudentsByDateAndLesson(\DateTime $dateTime, array $students, int $lesson) {
        $studentIds = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder();
        $qbInner = $this->em->createQueryBuilder();

        $qbInner
            ->select('sInner.id')
            ->from(Absence::class, 'aInner')
            ->leftJoin('aInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.memberships', 'mInner')
            ->leftJoin('mInner.student', 'sInner')
            ->where('aInner.date = :date')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('aInner.lessonStart'),
                    $qb->expr()->andX(
                        'aInner.lessonStart <= :lesson',
                        'aInner.lessonEnd >= :lesson'
                    )
                )
            );

        $qb
            ->select('s')
            ->from(Student::class, 's')
            ->where($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->andWhere($qb->expr()->in('s.id', ':students'))
            ->setParameter('date', $dateTime)
            ->setParameter('lesson', $lesson)
            ->setParameter('students', $studentIds);

        return $qb->getQuery()->getResult();
    }

    public function persist(Absence $person): void {
        $this->em->persist($person);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete(Absence::class, 'p')
            ->getQuery()
            ->execute();

        $this->flushIfNotInTransaction();
    }

}