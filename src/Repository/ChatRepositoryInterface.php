<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;

interface ChatRepositoryInterface {

    /**
     * @param User $user
     * @return Chat[]
     */
    public function findAllByUser(User $user): array;

    public function persist(Chat $chat): void;

    public function remove(Chat $chat): void;

    public function removeAll(): int;
}