<?php

namespace App\Repository;

use App\Entity\Checklist;
use App\Entity\ChecklistStudent;
use App\Entity\Student;
use App\Entity\UserType;

interface ChecklistStudentRepositoryInterface {
    public function countCheckedForChecklist(Checklist $checklist): int;

    public function countNotCheckedForChecklist(Checklist $checklist): int;

    /**
     * @param Student $student
     * @param bool $onlyNotChecked
     * @return ChecklistStudent[]
     */
    public function findAllByStudent(Student $student, bool $onlyNotChecked = false): array;
}