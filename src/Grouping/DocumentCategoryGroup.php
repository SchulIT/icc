<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\DocumentCategory;

class DocumentCategoryGroup implements GroupInterface, SortableGroupInterface {

    /** @var Document[] */
    private $documents;

    public function __construct(private DocumentCategory $category)
    {
    }

    public function getCategory(): DocumentCategory {
        return $this->category;
    }

    public function getDocuments(): array {
        return $this->documents;
    }

    public function getKey() {
        return $this->category;
    }

    public function addItem($item) {
        $this->documents[] = $item;
    }

    public function &getItems(): array {
        return $this->documents;
    }
}