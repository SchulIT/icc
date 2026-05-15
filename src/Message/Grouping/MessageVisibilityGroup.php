<?php

namespace App\Message\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Message\Entity\Message;
use App\Common\Entity\UserType;

/**
 * @implements GroupInterface<UserType, Message>
 */
class MessageVisibilityGroup implements GroupInterface {

    /**
     * @var Message[]
     */
    private array $messages = [ ];

    public function __construct(private readonly UserType $userType)
    {
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /**
     * @return UserType
     */
    public function getKey(): mixed {
        return $this->userType;
    }

    /**
     * @param Message $item
     */
    public function addItem($item): void {
        $this->messages[] = $item;
    }


}