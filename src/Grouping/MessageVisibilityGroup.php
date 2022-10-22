<?php

namespace App\Grouping;

use App\Entity\Message;
use App\Entity\UserType;

class MessageVisibilityGroup implements GroupInterface {

    /**
     * @var Message[]
     */
    private array $messages = [ ];

    public function __construct(private UserType $userType)
    {
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @return Message[]
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * @return UserType
     */
    public function getKey() {
        return $this->userType;
    }

    /**
     * @param Message $item
     */
    public function addItem($item) {
        $this->messages[] = $item;
    }


}