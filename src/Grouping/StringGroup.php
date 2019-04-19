<?php

namespace App\Grouping;

class StringGroup implements GroupInterface, SortableGroupInterface {

    /** @var string|null */
    private $key;

    /** @var array */
    private $items;

    public function __construct(?string $key) {
        $this->key = $key;
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