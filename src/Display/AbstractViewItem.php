<?php

namespace App\Display;

abstract class AbstractViewItem {

    public function __construct(private int $lesson, private bool $startsBefore)
    {
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function isStartsBefore(): bool {
        return $this->startsBefore;
    }

    public abstract function getName(): string;

    /**
     * Specifies the sorting index. Items with lower indices
     * will be sorted before others.
     */
    public abstract function getSortingIndex(): int;
}