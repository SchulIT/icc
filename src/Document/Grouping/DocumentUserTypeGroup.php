<?php

namespace App\Document\Grouping;

use App\Document\Entity\Document;
use App\Common\Entity\UserType;
use App\Framework\Grouping\GroupInterface;

class DocumentUserTypeGroup implements GroupInterface {
    /**
     * @var Document[]
     */
    private array $documents = [ ];

    public function __construct(private UserType $userType)
    {
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @return Document[]
     */
    public function getDocuments() {
        return $this->documents;
    }

    public function getKey() {
        return $this->userType;
    }

    /**
     * @param Document $item
     */
    public function addItem($item) {
        $this->documents[] = $item;
    }
}