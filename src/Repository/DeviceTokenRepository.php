<?php

namespace App\Repository;

use App\Entity\IcsAccessToken;
use App\Entity\User;

class DeviceTokenRepository extends AbstractRepository implements DeviceTokenRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(IcsAccessToken::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findAllBy(User $user): array {
        return $this->em
            ->getRepository(IcsAccessToken::class)
            ->findBy([
                'user' => $user
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(IcsAccessToken $token): void {
        $this->em->persist($token);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(IcsAccessToken $token): void {
        $this->em->remove($token);
        $this->em->flush();
    }
}