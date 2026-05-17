<?php

namespace App\Document\View\Filter;

use App\Document\Entity\DocumentCategory;

readonly class DocumentCategoryFilterView {
    public function __construct(
        public array $categories,
        public DocumentCategory|null $currentCategory = null
    ) {

    }
}