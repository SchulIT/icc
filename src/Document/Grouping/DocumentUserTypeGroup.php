<?php

namespace App\Document\Grouping;

use App\Document\Entity\Document;
use App\Common\Entity\UserType;
use App\Framework\Grouping\GroupInterface;

/**
 * @implements GroupInterface<UserType, Document>
 */
class DocumentUserTypeGroup implements GroupInterface {
    /**
     * @var Document[]
     */
    private array $documents = [ ];

    public function __construct(private readonly UserType $userType)
    {
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @return Document[]
     */
    public function getDocuments(): array {
        return $this->documents;
    }

    public function getKey(): UserType {
        return $this->userType;
    }

    /**
     * @param Document $item
     */
    public function addItem($item): void {
        $this->documents[] = $item;
    }
}