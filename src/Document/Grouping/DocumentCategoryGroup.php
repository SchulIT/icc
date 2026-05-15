<?php

namespace App\Document\Grouping;

use App\Document\Entity\Document;
use App\Document\Entity\DocumentCategory;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<DocumentCategory, Document>
 */
class DocumentCategoryGroup implements SortableGroupInterface {

    /** @var Document[] */
    private array $documents;

    public function __construct(private readonly DocumentCategory $category)
    {
    }

    public function getCategory(): DocumentCategory {
        return $this->category;
    }

    public function getDocuments(): array {
        return $this->documents;
    }

    public function getKey(): DocumentCategory {
        return $this->category;
    }

    public function addItem($item): void {
        $this->documents[] = $item;
    }

    public function &getItems(): array {
        return $this->documents;
    }
}