<?php

namespace App\Repository;

use App\Entity\ChatTag;
use App\Entity\UserType;

interface ChatTagRepositoryInterface {

    /**
     * @return ChatTag[]
     */
    public function findAll(): array;

    /**
     * @param UserType $userType
     * @return ChatTag[]
     */
    public function findForUserType(UserType $userType): array;

    public function persist(ChatTag $chatTag): void;

    public function remove(ChatTag $chatTag): void;
}