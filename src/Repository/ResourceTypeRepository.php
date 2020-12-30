<?php

namespace App\Repository;

use App\Entity\ResourceType;

class ResourceTypeRepository extends AbstractRepository implements ResourceTypeRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findRoomType(): ResourceType {
        return $this->em->getRepository(ResourceType::class)
            ->findOneBy([
                'id' => 1
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(ResourceType::class)
            ->findAll();
    }

    public function persist(ResourceType $type): void {
        $this->em->persist($type);
        $this->em->flush();
    }

    public function remove(ResourceType $type): void {
        $this->em->remove($type);
        $this->em->flush();
    }
}