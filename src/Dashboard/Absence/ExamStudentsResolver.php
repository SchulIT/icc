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

    private $em;
    private $examRepository;

    public function __construct(EntityManagerInterface $em, ExamRepositoryInterface $examRepository) {
        $this->em = $em;
        $this->examRepository = $examRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $students = ArrayUtils::createArrayWithKeys(
            $students,
            function(Student $student) {
                return $student->getId();
            }
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
            array_map(function ($row) {
                return $row['examId'];
            }, $result)
        );

        $exams = ArrayUtils::createArrayWithKeys(
            $this->examRepository->findAllByIds($examIds),
            function(Exam $exam) {
                return $exam->getId();
            });

        $absent = [ ];

        // STEP 3: compile list of absent students
        foreach($result as $row) {
            $absent[] = new AbsentExamStudent($students[$row['studentId']], $exams[$row['examId']]);
        }

        return $absent;
    }
}