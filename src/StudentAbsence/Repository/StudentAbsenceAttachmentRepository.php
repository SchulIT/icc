<?php

namespace App\StudentAbsence\Repository;

use App\Framework\Repository\AbstractRepository;
use App\StudentAbsence\Repository\StudentAbsenceAttachmentRepositoryInterface;
use App\StudentAbsence\Entity\StudentAbsenceAttachment;

class StudentAbsenceAttachmentRepository extends AbstractRepository implements StudentAbsenceAttachmentRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(StudentAbsenceAttachment::class)
            ->findAll();
    }

    public function remove(StudentAbsenceAttachment $attachment): void {
        $this->em->remove($attachment);
        $this->em->flush();
    }
}