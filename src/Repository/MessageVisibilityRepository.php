<?php

namespace App\Repository;

use App\Entity\MessageVisibility;

class MessageVisibilityRepository extends AbstractRepository implements MessageVisibilityRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(MessageVisibility::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(MessageVisibility $messageVisibility): void {
        $this->em->persist($messageVisibility);
        $this->em->flush();
    }
}