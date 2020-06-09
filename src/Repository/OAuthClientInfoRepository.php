<?php

namespace App\Repository;

use App\Entity\OAuthClientInfo;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;

class OAuthClientInfoRepository extends AbstractRepository implements OAuthClientInfoRepositoryInterface {

    public function persist(OAuthClientInfo $clientInfo): void {
        $this->em->persist($clientInfo);
        $this->em->flush();
    }

    public function findOneByClient(Client $client): ?OAuthClientInfo {
        return $this->em->getRepository(OAuthClientInfo::class)
            ->findOneBy([
                'client' => $client
            ]);
    }
}