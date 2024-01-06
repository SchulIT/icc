<?php

namespace App\Repository;

use App\Entity\ExamStudent;
use App\Entity\Student;
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