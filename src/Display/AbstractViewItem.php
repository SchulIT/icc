<?php

namespace App\Display;

abstract class AbstractViewItem {

    /** @var int */
    private $lesson;

    /** @var bool */
    private $startsBefore;

    public function __construct(int $lesson, bool $startsBefore) {
        $this->lesson = $lesson;
        $this->startsBefore = $startsBefore;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @return bool
     */
    public function isStartsBefore(): bool {
        return $this->startsBefore;
    }

    public abstract function getName(): string;

    /**
     * Specifies the sorting index. Items with lower indices
     * will be sorted before others.
     *
     * @return int
     */
    public abstract function getSortingIndex(): int;
}