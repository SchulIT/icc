<?php

namespace App\Repository;

use App\Entity\WikiArticleVisibility;

class WikiArticleVisibilityRepository extends AbstractRepository implements WikiArticleVisibilityRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(WikiArticleVisibility::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(WikiArticleVisibility $visibility): void {
        $this->em->persist($visibility);
        $this->em->flush();
    }
}