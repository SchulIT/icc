<?php

namespace App\Checklist\Repository;

use App\Checklist\Entity\Checklist;
use App\Checklist\Entity\ChecklistStudent;
use App\Common\Entity\Student;
use App\Common\Entity\UserType;

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