<?php

namespace App\Repository;

use App\Entity\OAuthClientInfo;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;

interface OAuthClientInfoRepositoryInterface {
    public function findOneByClient(Client $client): ?OAuthClientInfo;

    /**
     * @return OAuthClientInfo[]
     */
    public function findAll(): array;

    public function persist(OAuthClientInfo $clientInfo): void;
}