<?php

namespace App\Dashboard;

use App\Entity\Message;

class DashboardView {

    /** @var Message[] */
    private $messages = [ ];

    private $items = [ ];

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    public function getLessons(): array {
        $lessons = array_keys($this->items);
        sort($lessons, SORT_NUMERIC);

        return $lessons;
    }

    /**
     * @return AbstractViewItem[]
     */
    public function getItems(int $lesson): array {
        return $this->items[$lesson] ?? [ ];
    }

    public function addItem(int $lesson, AbstractViewItem $item): void {
        if(!isset($this->items[$lesson])) {
            $this->items[$lesson] = [ ];
        }

        $this->items[$lesson][] = $item;
    }

    public function addMessage(Message $message): void {
        $this->messages[] = $message;
    }
}