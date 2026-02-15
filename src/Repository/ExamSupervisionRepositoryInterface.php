<?php

namespace App\Repository;

use App\Entity\ExamSupervision;
use App\Entity\Teacher;
use DateTime;

interface ExamSupervisionRepositoryInterface {

    /**
     * @param Teacher $teacher
     * @param DateTime $dateTime
     * @return ExamSupervision[]
     */
    public function findByTeacherAndDate(Teacher $teacher, DateTime $dateTime): array;
}