<?php

namespace App\ParentsDay\Repository;

use App\Framework\Repository\AbstractRepository;
use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Entity\ParentsDayAppointment;
use App\Common\Entity\Teacher;
use App\ParentsDay\Repository\ParentsDayAppointmentRepositoryInterface;

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