<?php

namespace App\Grouping;

use App\Entity\Document;
use App\Entity\UserType;

class DocumentUserTypeGroup implements GroupInterface {
    /**
     * @var UserType
     */
    private $userType;

    /**
     * @var Document[]
     */
    private $documents = [ ];

    public function __construct(UserType $userType) {
        $this->userType = $userType;
    }

    /**
     * @return UserType
     */
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