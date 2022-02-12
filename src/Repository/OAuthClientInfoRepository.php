<?php

namespace App\Repository;

use App\Entity\OAuthClient;

class OAuthClientInfoRepository extends AbstractRepository implements OAuthClientInfoRepositoryInterface {

    public function persist(OAuthClient $clientInfo): void {
        $this->em->persist($clientInfo);
        $this->em->flush();
    }

    public function findOneByIdentifier(string $identifier): ?OAuthClient {
        return $this->em->getRepository(OAuthClient::class)
            ->findOneBy([
                'identifier' => $identifier
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(OAuthClient::class)
            ->findAll();
    }
}