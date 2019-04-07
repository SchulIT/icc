<?php

namespace App\Repository;

use App\Entity\MessageVisibility;

interface MessageVisibilityRepositoryInterface {

    /**
     * @return MessageVisibility[]
     */
    public function findAll(): array;

    /**
     * @param MessageVisibility $messageVisibility
     */
    public function persist(MessageVisibility $messageVisibility): void;
}