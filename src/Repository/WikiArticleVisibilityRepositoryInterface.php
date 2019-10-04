<?php

namespace App\Repository;

use App\Entity\WikiArticleVisibility;

interface WikiArticleVisibilityRepositoryInterface {

    /**
     * @return WikiArticleVisibility[]
     */
    public function findAll(): array;

    /**
     * @param WikiArticleVisibility $visibility
     */
    public function persist(WikiArticleVisibility $visibility): void;
}