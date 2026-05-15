<?php

namespace App\Message\Grouping;

use App\Framework\Grouping\SortableGroupInterface;
use App\Message\Entity\Message;

/**
 * @implements SortableGroupInterface<bool, Message>
 */
class MessageExpirationGroup implements SortableGroupInterface {
    /**
     * @var Message[]
     */
    private array $messages = [ ];

    public function __construct(private readonly bool $isExpired)
    {
    }

    public function isExpired(): bool {
        return $this->isExpired;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function getKey(): mixed {
        return $this->isExpired;
    }

    /**
     * @param Message $item
     */
    public function addItem($item): void {
        $this->messages[] = $item;
    }

    public function &getItems(): array {
        return $this->messages;
    }
}