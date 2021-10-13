<?php

namespace App\Repository;

use App\Entity\SickNoteAttachment;

class SickNoteAttachmentRepository extends AbstractRepository implements SickNoteAttachmentRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(SickNoteAttachment::class)
            ->findAll();
    }

    public function remove(SickNoteAttachment $attachment): void {
        $this->em->remove($attachment);
        $this->em->flush();
    }
}