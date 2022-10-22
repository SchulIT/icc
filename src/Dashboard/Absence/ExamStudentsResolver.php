<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentExamStudent;
use App\Entity\Exam;
use App\Entity\Student;
use App\Repository\ExamRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ExamStudentsResolver implements AbsenceResolveStrategyInterface {

    public function __construct(private EntityManagerInterface $em, private ExamRepositoryInterface $examRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $students = ArrayUtils::createArrayWithKeys(
            $students,
            fn(Student $student) => $student->getId()
        );

        // STEP 1: Resolve student -> exam relation (IDs only)
        $result = $this->em->createQueryBuilder()
            ->select(['s.id AS studentId', 'e.id AS examId'])
            ->from(Exam::class, 'e')
            ->leftJoin('e.students', 's')
            ->where('s.id IN (:students)')
            ->andWhere('e.date = :date')
            ->andWhere('e.lessonStart <= :lesson')
            ->andWhere('e.lessonEnd >= :lesson')
            ->setParameter('date', $dateTime)
            ->setParameter('lesson', $lesson)
            ->setParameter('students', array_keys($students))
            ->getQuery()
            ->getScalarResult();

        // STEP 2: Resolve attending exams
        $examIds = array_unique(
            array_map(fn($row) => $row['examId'], $result)
        );

        $exams = ArrayUtils::createArrayWithKeys(
            $this->examRepository->findAllByIds($examIds),
            fn(Exam $exam) => $exam->getId());

        $absent = [ ];

        // STEP 3: compile list of absent students
        foreach($result as $row) {
            $absent[] = new AbsentExamStudent($students[$row['studentId']], $exams[$row['examId']]);
        }

        return $absent;
    }
}