<?php

namespace App\Document\Grouping;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentCategory;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

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