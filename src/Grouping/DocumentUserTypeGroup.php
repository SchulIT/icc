<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\UserType;

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