<?php

namespace App\Wiki;

use App\Entity\WikiArticle;

class TreeHelper {

    /**
     * @param WikiArticle[] $root
     * @param bool $pathKey Whether to include the path as an array key
     * @param WikiArticle|null $excludeChildren Excludes all children of the specified wiki article
     * @return WikiArticle[] Flat list of wiki articles
     */
    public function flattenTree(array $root, bool $pathKey = true, WikiArticle|null $excludeChildren = null): array {
        $result = [ ];

        foreach($root as $article) {
            $result += $this->internalFlattenTree($article, '', $excludeChildren);
        }

        if($pathKey === false) {
            $result = array_values($result);
        }

        return $result;
    }

    private function internalFlattenTree(WikiArticle $article, string $path, WikiArticle|null $excludeChildren = null): array {
        $result = [ ];
        $path = sprintf('%s / %s', $path, $article->getTitle());

        $result[$path] = $article;

        if($excludeChildren === null || $article->getId() !== $excludeChildren->getId()) {
            foreach ($article->getChildren() as $child) {
                $result += $this->internalFlattenTree($child, $path, $excludeChildren);
            }
        }

        return $result;
    }
}