<?php

namespace App\Repository;

use App\Entity\MessageVisibility;
use Doctrine\ORM\EntityManagerInterface;

class MessageVisibilityRepository implements MessageVisibilityRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

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