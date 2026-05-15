<?php

namespace App\Exam\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamStudent;
use App\Common\Entity\Student;
use App\Exam\Repository\ExamStudentRepositoryInterface;
use DateTime;

class ExamStudentRepository extends AbstractRepository implements ExamStudentRepositoryInterface {

    public function findAllByStudentsAndLesson(array $students, DateTime $date, int $lesson): array {
        $studentIds = array_map(
            fn(Student $student) => $student->getId(),
            $students
        );

        return $this->em->createQueryBuilder()
            ->select(['es', 'e', 's', 't'])
            ->from(ExamStudent::class, 'es')
            ->leftJoin('es.student', 's')
            ->leftJoin('es.exam', 'e')
            ->leftJoin('es.tuition', 't')
            ->where('s.id IN (:students)')
            ->andWhere('e.date = :date')
            ->andWhere('e.lessonStart <= :lesson')
            ->andWhere('e.lessonEnd >= :lesson')
            ->setParameter('date', $date)
            ->setParameter('lesson', $lesson)
            ->setParameter('students', $studentIds)
            ->getQuery()
            ->getResult();
    }
}