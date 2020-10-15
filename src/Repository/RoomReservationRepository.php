<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Entity\Teacher;
use DateTime;

class RoomReservationRepository extends AbstractRepository implements RoomReservationRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(RoomReservation::class)
            ->findAll();
    }

    public function findOneByDateAndRoomAndLesson(DateTime $dateTime, Room $room, int $lessonNumber): ?RoomReservation {
        $qb = $this->em->createQueryBuilder()
            ->select(['r', 'rt', 'rr'])
            ->from(RoomReservation::class, 'r')
            ->leftJoin('r.teacher', 'rt')
            ->leftJoin('r.room', 'rr')
            ->where('r.date = :date')
            ->andWhere('rr.id = :room')
            ->andWhere('r.lessonStart <= :lesson')
            ->andWhere('r.lessonEnd >= :lesson')
            ->setParameter('room', $room->getId())
            ->setParameter('date', $dateTime)
            ->setParameter('lesson', $lessonNumber)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $date): array {
        return $this->em->getRepository(RoomReservation::class)
            ->findBy([
                'date' => $date
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByRoomAndDate(Room $room, DateTime $dateTime): array {
        return $this->em->getRepository(RoomReservation::class)
            ->findBy([
                'date' => $dateTime,
                'room' => $room
            ]);
    }

    public function persist(RoomReservation $reservation): void {
        $this->em->persist($reservation);
        $this->em->flush();
    }

    public function remove(RoomReservation $reservation): void {
        $this->em->remove($reservation);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findAllByRoomAndTeacher(?Room $room, ?Teacher $teacher, ?DateTime $from): array {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(RoomReservation::class, 'r')
            ->leftJoin('r.room', 'rr')
            ->leftJoin('r.teacher', 'rt');

        if($room !== null) {
            $qb->andWhere('rr.id = :room')
                ->setParameter('room', $room->getId());
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
            ->from(RoomReservation::class, 'r')
            ->leftJoin('r.room', 'rr')
            ->leftJoin('r.teacher', 'rt')
            ->andWhere('rt.id = :teacher')
            ->setParameter('teacher', $teacher->getId());

        if($date !== null) {
            $qb->andWhere('r.date = :date')
                ->setParameter('date', $date);
        }

        return $qb->getQuery()->getResult();
    }
}