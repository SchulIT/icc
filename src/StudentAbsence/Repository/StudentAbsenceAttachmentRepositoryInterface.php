<?php

namespace App\StudentAbsence\Repository;

use App\StudentAbsence\Entity\StudentAbsenceAttachment;

interface StudentAbsenceAttachmentRepositoryInterface {
    /**
     * @return StudentAbsenceAttachment[]
     */
    public function findAll(): array;

    public function remove(StudentAbsenceAttachment $attachment): void;
}