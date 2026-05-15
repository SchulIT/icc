<?php

namespace App\Common\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Common\Entity\IcsAccessToken;
use App\Common\Entity\User;
use App\Common\Repository\DeviceTokenRepositoryInterface;

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