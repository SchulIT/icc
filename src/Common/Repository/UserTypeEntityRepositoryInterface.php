<?php

namespace App\Common\Repository;

use App\Common\Entity\UserTypeEntity;

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