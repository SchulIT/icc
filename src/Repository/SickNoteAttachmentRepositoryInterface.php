<?php

namespace App\Repository;

use App\Entity\SickNoteAttachment;

interface SickNoteAttachmentRepositoryInterface {
    /**
     * @return SickNoteAttachment[]
     */
    public function findAll(): array;

    public function remove(SickNoteAttachment $attachment): void;
}