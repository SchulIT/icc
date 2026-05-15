<?php

namespace App\Book\Repository;

use App\Book\Entity\BookComment;
use App\Book\Entity\BookEvent;
use App\Framework\Repository\AbstractRepository;
use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\Teacher;
use App\Book\Repository\BookEventRepositoryInterface;
use DateTime;

class BookEventRepository extends AbstractRepository implements BookEventRepositoryInterface {

    public function persist(BookEvent $bookEvent): void {
        $this->em->persist($bookEvent);
        $this->em->flush();
    }

    public function remove(BookEvent $bookEvent): void {
        $this->em->remove($bookEvent);
        $this->em->flush();
    }

    public function findByTeacher(Teacher $teacher, DateTime $start, DateTime $end): array {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(BookEvent::class, 'e')
            ->where('e.teacher = :teacher')
            ->andWhere('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('teacher', $teacher);

        return $qb->getQuery()->getResult();
    }

    public function findByStudent(Student $student, DateTime $start, DateTime $end): array {
        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(BookEvent::class, 'eInner')
            ->leftJoin('eInner.attendances', 'aInner')
            ->leftJoin('aInner.student', 'sInner')
            ->where('sInner.id = :student');

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(BookEvent::class, 'e')
            ->where('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('student', $student);

        $qb->andWhere(
            $qb->expr()->in('e.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    public function findByGrade(Grade $grade, Section $section, DateTime $start, DateTime $end): array {
        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(BookEvent::class, 'eInner')
            ->leftJoin('eInner.attendances', 'aInner')
            ->leftJoin('aInner.student', 'sInner')
            ->leftJoin('sInner.gradeMemberships', 'gmInner')
            ->where('gmInner.grade = :grade')
            ->andWhere('gmInner.section = :section');

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(BookEvent::class, 'e')
            ->where('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('grade', $grade)
            ->setParameter('section', $section);

        $qb->andWhere(
            $qb->expr()->in('e.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    public function removeRange(DateTime $start, DateTime $end): int {
        return $this->em->createQueryBuilder()
            ->delete(BookEvent::class, 'e')
            ->where('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
    }
}