<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayParentalInformation;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use function Doctrine\ORM\QueryBuilder;

class ParentsDayParentalInformationRepository extends AbstractTransactionalRepository implements ParentsDayParentalInformationRepositoryInterface {

    public function findForStudent(ParentsDay $parentsDay, Student $student): array {
        return $this->em->getRepository(ParentsDayParentalInformation::class)
            ->findBy([
                'parentsDay' => $parentsDay,
                'student' => $student
            ]);
    }

    public function findForTeacherAndStudents(ParentsDay $parentsDay, Teacher $teacher, array $students): array {
        $studentIds = array_map(
            fn(Student $student) => $student->getId(),
            $students
        );

        return $this->em->createQueryBuilder()
            ->select(['i', 't', 's'])
            ->from(ParentsDayParentalInformation::class, 'i')
            ->leftJoin('i.student', 's')
            ->leftJoin('i.teacher', 't')
            ->where('t.id = :teacher')
            ->andWhere('s.id IN (:students)')
            ->setParameter('teacher', $teacher)
            ->setParameter('students', $studentIds)
            ->getQuery()
            ->getResult();
    }

    public function persist(ParentsDayParentalInformation $information): void {
        $this->em->persist($information);
        $this->flushIfNotInTransaction();
    }
}