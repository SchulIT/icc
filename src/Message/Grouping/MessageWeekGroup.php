<?php

namespace App\Message\Grouping;

use App\Framework\Date\WeekOfYear;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\Message\Entity\Message;

/**
 * @implements SortableGroupInterface<WeekOfYear, Message>
 */
class MessageWeekGroup implements SortableGroupInterface {

    /** @var Message[] */
    private array $messages = [ ];

    public function __construct(private readonly WeekOfYear $week)
    {
    }

    public function getWeek(): WeekOfYear {
        return $this->week;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /**
     * @return WeekOfYear
     */
    public function getKey(): WeekOfYear {
        return $this->week;
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