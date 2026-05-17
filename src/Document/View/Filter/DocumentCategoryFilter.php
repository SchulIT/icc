<?php

namespace App\Document\View\Filter;

use App\Document\Entity\DocumentCategory;
use App\Document\Repository\DocumentCategoryRepositoryInterface;
use App\Framework\Utils\ArrayUtils;

readonly class DocumentCategoryFilter {

    public function __construct(
        private DocumentCategoryRepositoryInterface $repository
    ) {

    }

    public function handle(string|null $categoryUuid): DocumentCategoryFilterView {
        $categories = ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(DocumentCategory $category): string => $category->getUuid()->toString(),
        );

        $currentCategory = null;

        if($categoryUuid !== null) {
            $currentCategory = $categories[$categoryUuid] ?? null;
        }

        return new DocumentCategoryFilterView($categories, $currentCategory);
    }
}