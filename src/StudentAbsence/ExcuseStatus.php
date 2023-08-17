<?php

namespace App\StudentAbsence;

class ExcuseStatus {

    /**
     * @param ExcuseStatusItem[] $items
     */
    public function __construct(private readonly array $items) {

    }

    /**
     * @return ExcuseStatusItem[]
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function isCompletelyExcused(): bool {
        foreach($this->items as $item) {
            if($item->isExcused() === false) {
                return false;
            }
        }

        return true;
    }
}