<?php

namespace App\Repository;

use App\Entity\DeviceToken;
use App\Entity\User;

interface DeviceTokenRepositoryInterface {

    /**
     * @return DeviceToken[]
     */
    public function findAll(): array;

    /**
     * @param User $user
     * @return DeviceToken[]
     */
    public function findAllBy(User $user): array;

    /**
     * @param DeviceToken $token
     */
    public function persist(DeviceToken $token): void;

    /**
     * @param DeviceToken $token
     */
    public function remove(DeviceToken $token): void;
}