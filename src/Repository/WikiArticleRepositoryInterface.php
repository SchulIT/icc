<?php

namespace App\Repository;

use App\Entity\WikiArticle;

interface WikiArticleRepositoryInterface {

    /**
     * @return WikiArticle[]
     */
    public function findAll(): array;

    /**
     * @param string $q
     * @return WikiArticle[]
     */
    public function findAllByQuery(string $q): array;

    /**
     * @param WikiArticle $article
     */
    public function persist(WikiArticle $article): void;

    /**
     * @param WikiArticle $article
     */
    public function remove(WikiArticle $article): void;
}