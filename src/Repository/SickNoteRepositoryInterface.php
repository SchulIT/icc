<?php

namespace App\Repository;

use App\Entity\SickNote;
use App\Entity\User;
use DateTime;

interface SickNoteRepositoryInterface {
    /**
     * @param User $user
     * @return SickNote[]
     */
    public function findByUser(User $user): array;

    /**
     * @param DateTime $threshold
     * @return int Number of removed sick notes
     */
    public function removeExpired(DateTime $threshold): int;

    public function persist(SickNote $note): void;

    public function remove(SickNote $note): void;
}