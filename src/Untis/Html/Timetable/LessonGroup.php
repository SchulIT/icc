<?php

namespace App\Untis\Html\Timetable;

use App\Framework\Grouping\GroupInterface;

/**
 * @implements GroupInterface<string, Lesson>
 */
class LessonGroup implements GroupInterface {

    /** @var Lesson[] */
    private array $lessons;

    public function __construct(private readonly string $key)
    {
    }

    public function getKey(): string {
        return $this->key;
    }

    /**
     * @param Lesson $item
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