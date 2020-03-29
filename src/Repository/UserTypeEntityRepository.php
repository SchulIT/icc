<?php

namespace App\Repository;

use App\Entity\UserTypeEntity;

class UserTypeEntityRepository extends AbstractRepository implements UserTypeEntityRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(UserTypeEntity::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(UserTypeEntity $userTypeEntity): void {
        $this->em->persist($userTypeEntity);
        $this->em->flush();
    }
}