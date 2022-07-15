<?php

namespace App\Repository;

use App\Entity\StudentAbsenceAttachment;

interface StudentAbsenceAttachmentRepositoryInterface {
    /**
     * @return StudentAbsenceAttachment[]
     */
    public function findAll(): array;

    public function remove(StudentAbsenceAttachment $attachment): void;
}