<?php

namespace App\Repository;

use App\Entity\RoomTag;

class RoomTagRepository extends AbstractRepository implements RoomTagRepositoryInterface {

    public function findAll(): array {
        return $this->em->getRepository(RoomTag::class)
            ->findBy( [], [
                'name' => 'asc'
            ]);
    }

    public function persist(RoomTag $roomTag): void {
        $this->em->persist($roomTag);
        $this->em->flush();
    }

    public function remove(RoomTag $roomTag): void {
        $this->em->persist($roomTag);
        $this->em->flush();
    }
}