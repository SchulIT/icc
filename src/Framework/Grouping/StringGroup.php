<?php

namespace App\Framework\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

class StringGroup implements GroupInterface, SortableGroupInterface {

    private ?array $items = null;

    public function __construct(private ?string $key)
    {
    }

    public function getKey() {
        return $this->key;
    }

    public function addItem($item) {
        $this->items[] = $item;
    }

    public function &getItems(): array {
        return $this->items;
    }
}