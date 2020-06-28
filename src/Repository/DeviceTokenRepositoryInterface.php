<?php

namespace App\Repository;

use App\Entity\IcsAccessToken;
use App\Entity\User;

interface DeviceTokenRepositoryInterface {

    /**
     * @return IcsAccessToken[]
     */
    public function findAll(): array;

    /**
     * @param User $user
     * @return IcsAccessToken[]
     */
    public function findAllBy(User $user): array;

    /**
     * @param IcsAccessToken $token
     */
    public function persist(IcsAccessToken $token): void;

    /**
     * @param IcsAccessToken $token
     */
    public function remove(IcsAccessToken $token): void;
}