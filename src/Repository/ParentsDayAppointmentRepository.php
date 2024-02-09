<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayAppointment;
use App\Entity\Teacher;

class ParentsDayAppointmentRepository extends AbstractRepository implements ParentsDayAppointmentRepositoryInterface {



    public function findForTeacher(Teacher $teacher, ParentsDay $parentsDay): array {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(ParentsDayAppointment::class, 'a')
            ->leftJoin('a.teachers', 't')
            ->where('a.parentsDay = :day')
            ->andWhere('t = :teacher')
            ->setParameter('day', $parentsDay)
            ->setParameter('teacher', $teacher)
            ->getQuery()
            ->getResult();
    }

    public function findForStudents(array $students, ParentsDay $parentsDay): array {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(ParentsDayAppointment::class, 'a')
            ->leftJoin('a.students', 's')
            ->where('a.parentsDay = :day')
            ->andWhere('s IN (:students)')
            ->setParameter('day', $parentsDay)
            ->setParameter('students', $students)
            ->getQuery()
            ->getResult();
    }

    public function persist(ParentsDayAppointment $appointment): void {
        $this->em->persist($appointment);
        $this->em->flush();
    }

    public function remove(ParentsDayAppointment $appointment): void {
        $this->em->remove($appointment);
        $this->em->flush();
    }
}