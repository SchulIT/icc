<?php

namespace App\Exam\Repository;

use App\Exam\Entity\ExamSupervision;
use App\Common\Entity\Teacher;
use DateTime;

interface ExamSupervisionRepositoryInterface {

    /**
     * @param Teacher $teacher
     * @param DateTime $dateTime
     * @return ExamSupervision[]
     */
    public function findByTeacherAndDate(Teacher $teacher, DateTime $dateTime): array;
}