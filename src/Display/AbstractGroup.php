<?php

namespace App\Display;

use App\Common\Entity\Grade;
use App\Common\Entity\Teacher;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<Grade|Teacher, AbstractViewItem>
 */
abstract class AbstractGroup implements SortableGroupInterface {
    /** @var AbstractViewItem[] */
    private array $items = [ ];

    public function addItem($item): void {
        $this->items[] = $item;
    }

    public final function &getItems(): array {
        return $this->items;
    }

    public abstract function getKey(): mixed;

    public abstract function getHeader(): string;
}