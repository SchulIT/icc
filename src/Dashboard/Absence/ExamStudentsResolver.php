<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentExamStudent;
use App\Exam\Entity\Exam;
use App\Common\Entity\Student;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Exam\Repository\ExamStudentRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ExamStudentsResolver implements AbsenceResolveStrategyInterface {

    public function __construct(private readonly ExamStudentRepositoryInterface $examStudentRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $absent = [ ];

        foreach($this->examStudentRepository->findAllByStudentsAndLesson(ArrayUtils::iterableToArray($students), $dateTime, $lesson) as $examStudent) {
            $absent[] = new AbsentExamStudent($examStudent->getStudent(), $examStudent->getExam(), $examStudent->getTuition());
        }

        return $absent;
    }
}