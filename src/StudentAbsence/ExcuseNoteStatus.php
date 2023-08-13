<?php

namespace App\StudentAbsence;

class ExcuseNoteStatus {

    /**
     * @param ExcuseNoteStatusItem[] $items
     * @param bool $isCompletelyExcused
     */
    public function __construct(private readonly array $items, private readonly bool $isCompletelyExcused) {

    }

    /**
     * @return ExcuseNoteStatusItem[]
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function isCompletelyExcused(): bool {
        return $this->isCompletelyExcused;
    }
}