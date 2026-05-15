<?php

namespace App\Exam\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Exam\Entity\ExamSupervision;
use App\Common\Entity\Teacher;
use App\Exam\Repository\ExamSupervisionRepositoryInterface;
use DateTime;
use Override;

class ExamSupervisionRepository extends AbstractRepository implements ExamSupervisionRepositoryInterface {

    #[Override]
    public function findByTeacherAndDate(Teacher $teacher, DateTime $dateTime): array {
        return $this->em->createQueryBuilder()
            ->select(['s', 'e'])
            ->from(ExamSupervision::class, 's')
            ->leftJoin('s.exam', 'e')
            ->leftJoin('s.teacher', 't')
            ->where('s.teacher = :teacher')
            ->andWhere('e.date = :date')
            ->orderBy('s.lesson', 'asc')
            ->setParameter('teacher', $teacher)
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->getResult();
    }
}