<?php

namespace App\Exam\Repository;

use App\Exam\Entity\ExamStudent;
use App\Common\Entity\Student;
use DateTime;

interface ExamStudentRepositoryInterface {

    /**
     * @param Student[] $students
     * @param DateTime $date
     * @param int $lesson
     * @return ExamStudent[]
     */
    public function findAllByStudentsAndLesson(array $students, DateTime $date, int $lesson): array;
}