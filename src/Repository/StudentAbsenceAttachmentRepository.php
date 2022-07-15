<?php

namespace App\Repository;

use App\Entity\StudentAbsenceAttachment;

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