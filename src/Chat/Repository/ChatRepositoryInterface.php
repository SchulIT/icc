<?php

namespace App\Chat\Repository;

use App\Chat\Entity\Chat;
use App\Common\Entity\User;

interface ChatRepositoryInterface {

    /**
     * @param User $user
     * @param bool $archived
     * @return Chat[]
     */
    public function findAllByUser(User $user, bool $archived): array;

    public function persist(Chat $chat): void;

    public function remove(Chat $chat): void;

    public function archiveAll(): int;

    public function removeAll(): int;
}