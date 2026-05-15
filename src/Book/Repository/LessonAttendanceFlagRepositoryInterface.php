<?php

namespace App\Book\Repository;

use App\Book\Entity\AttendanceFlag;
use App\Common\Entity\Subject;

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