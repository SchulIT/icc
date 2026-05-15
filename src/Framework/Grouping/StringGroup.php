<?php

namespace App\Framework\Grouping;

/**
 * @implements SortableGroupInterface<string, string>
 */
class StringGroup implements SortableGroupInterface {

    private ?array $items = null;

    public function __construct(private ?string $key)
    {
    }

    public function getKey(): ?string {
        return $this->key;
    }

    public function addItem($item): void {
        $this->items[] = $item;
    }

    public function &getItems(): array {
        return $this->items;
    }
}