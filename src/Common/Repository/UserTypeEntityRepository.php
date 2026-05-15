<?php

namespace App\Common\Repository;

use App\Common\Entity\UserTypeEntity;
use App\Framework\Repository\AbstractRepository;
use App\Common\Repository\UserTypeEntityRepositoryInterface;

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