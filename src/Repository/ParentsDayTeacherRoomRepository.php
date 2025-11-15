<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayTeacherRoom;
use App\Entity\Room;
use App\Entity\Teacher;
use Override;

class ParentsDayTeacherRoomRepository extends AbstractTransactionalRepository implements ParentsDayTeacherRoomRepositoryInterface {

    #[Override]
    public function findAllByParentsDay(ParentsDay $parentsDay): array {
        return $this->em->getRepository(ParentsDayTeacherRoom::class)->findBy(['parentsDay' => $parentsDay]);
    }

    #[Override]
    public function persist(ParentsDayTeacherRoom $parentsDayTeacherRoom): void {
        $this->em->persist($parentsDayTeacherRoom);
        $this->flushIfNotInTransaction();
    }

    #[Override]
    public function remove(ParentsDayTeacherRoom $parentsDayTeacherRoom): void {
        $this->em->remove($parentsDayTeacherRoom);
        $this->flushIfNotInTransaction();
    }

    #[Override]
    public function removeByParentsDay(ParentsDay $parentsDayTeacherRoom): int {
        return $this->em
            ->createQueryBuilder()
            ->delete(ParentsDayTeacherRoom::class, 'r')
            ->where('r.parentsDay = :parentsDayTeacherRoom')
            ->setParameter('parentsDayTeacherRoom', $parentsDayTeacherRoom)
            ->getQuery()
            ->execute();
    }

    #[Override]
    public function findRoomByTeacherAndParentsDay(Teacher $teacher, ParentsDay $parentsDay): ?Room {
        $qb = $this->em->createQueryBuilder();

        return $qb
            ->select('r')
            ->from(Room::class, 'r')
            ->where(
                $qb->expr()->in(
                    'r.id',
                    $this->em->createQueryBuilder()
                        ->select('trr.id')
                        ->from(ParentsDayTeacherRoom::class, 'tr')
                        ->leftJoin('tr.room', 'trr')
                        ->where('tr.parentsDay = :parentsDay')
                        ->andWhere('tr.teacher = :teacher')
                        ->getDQL()
                )
            )
            ->setParameter('parentsDay', $parentsDay)
            ->setParameter('teacher', $teacher)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}