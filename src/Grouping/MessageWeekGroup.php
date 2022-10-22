<?php

namespace App\Grouping;

use App\Date\WeekOfYear;
use App\Entity\Message;

class MessageWeekGroup implements GroupInterface, SortableGroupInterface {

    /** @var Message[] */
    private array $messages = [ ];

    public function __construct(private WeekOfYear $week)
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
    public function getKey() {
        return $this->week;
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