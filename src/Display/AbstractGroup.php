<?php

namespace App\Display;

use App\Grouping\SortableGroupInterface;

abstract class AbstractGroup implements SortableGroupInterface {
    /** @var AbstractViewItem[] */
    private array $items = [ ];

    public function addItem(AbstractViewItem $item): void {
        $this->items[] = $item;
    }

    public final function &getItems(): array {
        return $this->items;
    }

    public abstract function getHeader(): string;
}