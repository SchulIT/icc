<?php

namespace App\Repository;

use App\Entity\UserTypeEntity;

interface UserTypeEntityRepositoryInterface {

    /**
     * @return UserTypeEntity[]
     */
    public function findAll(): array;

    /**
     * @param UserTypeEntity $userTypeEntity
     */
    public function persist(UserTypeEntity $userTypeEntity): void;
}