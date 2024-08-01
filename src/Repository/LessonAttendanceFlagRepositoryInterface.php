<?php

namespace App\Repository;

use App\Entity\AttendanceFlag;
use App\Entity\Subject;

interface LessonAttendanceFlagRepositoryInterface {

    /**
     * @return AttendanceFlag[]
     */
    public function findAll(): array;

    /**
     * @param Subject $subject
     * @return AttendanceFlag[]
     */
    public function findAllBySubject(Subject $subject): array;

    public function persist(AttendanceFlag $flag): void;

    public function remove(AttendanceFlag $flag): void;
}