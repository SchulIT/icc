<?php

namespace App\Dashboard;

use App\Entity\Message;

class DashboardView {

    /** @var Message[] */
    private $messages = [ ];

    private $items = [ ];

    private $beforeItems = [ ];

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    public function getLessons(): array {
        $lessons = array_merge(array_keys($this->items), array_keys($this->beforeItems));
        sort($lessons, SORT_NUMERIC);

        return $lessons;
    }

    /**
     * @return AbstractViewItem[]
     */
    public function getItems(int $lesson): array {
        return $this->items[$lesson] ?? [ ];
    }

    /**
     * @return AbstractViewItem[]
     */
    public function getItemsBefore(int $lesson): array {
        return $this->beforeItems[$lesson] ?? [ ];
    }

    public function addItem(int $lesson, AbstractViewItem $item): void {
        if(!isset($this->items[$lesson])) {
            $this->items[$lesson] = [ ];
        }

        $this->items[$lesson][] = $item;
    }

    public function addItemBefore(int $lesson, AbstractViewItem $item): void {
        if(!isset($this->beforeItems[$lesson])) {
            $this->beforeItems[$lesson] = [ ];
        }

        $this->beforeItems[$lesson][] = $item;
    }

    public function addMessage(Message $message): void {
        $this->messages[] = $message;
    }
}