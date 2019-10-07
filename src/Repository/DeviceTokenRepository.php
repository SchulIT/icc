<?php

namespace App\Repository;

use App\Entity\DeviceToken;
use App\Entity\User;

class DeviceTokenRepository extends AbstractRepository implements DeviceTokenRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(DeviceToken::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findAllBy(User $user): array {
        return $this->em
            ->getRepository(DeviceToken::class)
            ->findBy([
                'user' => $user
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(DeviceToken $token): void {
        $this->em->persist($token);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(DeviceToken $token): void {
        $this->em->remove($token);
        $this->em->flush();
    }
}