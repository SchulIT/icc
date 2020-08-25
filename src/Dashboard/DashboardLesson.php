<?php

namespace App\Dashboard;

use phpDocumentor\Reflection\Types\This;

class DashboardLesson {
    /** @var int */
    private $lessonNumber;

    /** @var bool */
    private $isBefore;

    /** @var bool */
    private $hasWarning = false;

    /** @var AbstractViewItem[] */
    private $items = [ ];

    public function __construct(int $lessonNumber, bool $isBefore) {
        $this->lessonNumber = $lessonNumber;
        $this->isBefore = $isBefore;
    }

    public function isBefore(): bool {
        return $this->isBefore;
    }

    public function getLessonNumber(): int {
        return $this->lessonNumber;
    }

    public function hasWarning(): bool {
        return $this->hasWarning;
    }

    public function setWarning(): void {
        $this->hasWarning = true;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function clearItems(): void {
        $this->items = [ ];
    }

    public function replaceItems(array $items): void {
        $this->items = $items;
    }

    public function addItem(AbstractViewItem $item) {
        $this->items[] = $item;
    }

    /**
     * Removes all timetable lessons from the lesson
     */
    public function removeLessons(): void {
        foreach($this->items as $idx => $item) {
            if($item instanceof TimetableLessonViewItem || $item instanceof SupervisionViewItem) {
                unset($this->items[$idx]);
            }
        }
    }
}