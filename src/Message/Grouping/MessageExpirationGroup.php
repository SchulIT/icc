<?php

namespace App\Message\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\Message\Entity\Message;

class MessageExpirationGroup implements GroupInterface, SortableGroupInterface {
    /**
     * @var Message[]
     */
    private array $messages = [ ];

    public function __construct(private bool $isExpired)
    {
    }

    public function isExpired(): bool {
        return $this->isExpired;
    }

    /**
     * @return Message[]
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function getKey() {
        return $this->isExpired;
    }

    /**
     * @param Message $item
     */
    public function addItem($item) {
        $this->messages[] = $item;
    }

    public function &getItems(): array {
        return $this->messages;
    }
}