<?php

namespace App\Repository;

use App\Entity\LessonAttendanceFlag;
use App\Entity\Subject;

interface LessonAttendanceFlagRepositoryInterface {

    /**
     * @return LessonAttendanceFlag[]
     */
    public function findAll(): array;

    /**
     * @param Subject $subject
     * @return LessonAttendanceFlag[]
     */
    public function findAllBySubject(Subject $subject): array;

    public function persist(LessonAttendanceFlag $flag): void;

    public function remove(LessonAttendanceFlag $flag): void;
}