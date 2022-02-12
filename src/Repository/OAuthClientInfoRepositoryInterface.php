<?php

namespace App\Repository;

use App\Entity\OAuthClient;

interface OAuthClientInfoRepositoryInterface {
    public function findOneByIdentifier(string $identifier): ?OAuthClient;

    /**
     * @return OAuthClient[]
     */
    public function findAll(): array;

    public function persist(OAuthClient $clientInfo): void;
}