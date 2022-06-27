<?php

namespace App\Untis\Html\Timetable;

use App\Grouping\GroupInterface;

class LessonGroup implements GroupInterface {

    private string $key;

    /** @var Lesson[] */
    private array $lessons;

    public function __construct(string $key) {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @param Lesson $item
     * @return void
     */
    public function addItem($item): void {
        $this->lessons[] = $item;
    }

    /**
     * @return Lesson[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

}