<?php

namespace App\Repository;

use App\Entity\ResourceEntity;
use App\Entity\ResourceReservation;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use DateTime;

class ResourceReservationRepository extends AbstractRepository implements ResourceReservationRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(ResourceReservation::class)
            ->findAll();
    }

    public function findOneByDateAndResourceAndLesson(DateTime $dateTime, ResourceEntity $resource, int $lessonNumber): ?ResourceReservation {
        $qb = $this->em->createQueryBuilder()
            ->select(['r', 'rt', 'rr'])
            ->from(ResourceReservation::class, 'r')
            ->leftJoin('r.teacher', 'rt')
            ->leftJoin('r.resource', 'rr')
            ->where('r.date = :date')
            ->andWhere('rr.id = :resource')
            ->andWhere('r.lessonStart <= :lesson')
            ->andWhere('r.lessonEnd >= :lesson')
            ->setParameter('resource', $resource->getId())
            ->setParameter('date', $dateTime)
            ->setParameter('lesson', $lessonNumber)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $date): array {
        return $this->em->getRepository(ResourceReservation::class)
            ->findBy([
                'date' => $date
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByResourceAndDate(ResourceEntity $resource, DateTime $dateTime): array {
        return $this->em->getRepository(ResourceReservation::class)
            ->findBy([
                'date' => $dateTime,
                'resource' => $resource
            ]);
    }

    public function persist(ResourceReservation $reservation): void {
        $this->em->persist($reservation);
        $this->em->flush();
    }

    public function remove(ResourceReservation $reservation): void {
        $this->em->remove($reservation);
        $this->em->flush();
    }

    public function removeBetween(DateTime $start, DateTime $end): int {
        return $this->em->createQueryBuilder()
            ->delete(ResourceReservation::class, 'r')
            ->where('r.date >= :start')
            ->andWhere('r.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
    }

    /**
     * @inheritDoc
     */
    public function findAllByRoomAndTeacher(?ResourceEntity $resource, ?Teacher $teacher, ?DateTime $from): array {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(ResourceReservation::class, 'r')
            ->leftJoin('r.resource', 'rr')
            ->leftJoin('r.teacher', 'rt');

        if($resource !== null) {
            $qb->andWhere('rr.id = :resource')
                ->setParameter('resource', $resource->getId());
        }

        if($teacher !== null) {
            $qb->andWhere('rt.id = :teacher')
                ->setParameter('teacher', $teacher->getId());
        }

        if($from !== null) {
            $qb->andWhere('r.date >= :from')
                ->setParameter('from', $from);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacherAndDate(Teacher $teacher, DateTime $date): array {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(ResourceReservation::class, 'r')
            ->leftJoin('r.resource', 'rr')
            ->leftJoin('r.teacher', 'rt')
            ->andWhere('rt.id = :teacher')
            ->setParameter('teacher', $teacher->getId());

        if($date !== null) {
            $qb->andWhere('r.date = :date')
                ->setParameter('date', $date);
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllByStudentAndDate(Student $student, DateTime $date): array {
        $qbInner = $this->em->createQueryBuilder()
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.memberships', 'mInner')
            ->leftJoin('mInner.student', 'sInner')
            ->where('sInner.id = :student');

        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(ResourceReservation::class, 'r')
            ->leftJoin('r.resource', 'rr')
            ->leftJoin('r.teacher', 'rt');

        $qb->where(
            $qb->expr()->in('r.associatedStudyGroup', $qbInner->getDQL())
        )
            ->setParameter('student', $student->getId())
            ->andWhere('r.date = :date')
                ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }
}