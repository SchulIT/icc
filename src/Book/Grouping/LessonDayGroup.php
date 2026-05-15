<?php

namespace App\Book\Grouping;

use App\Book\Lesson;
use App\Framework\Grouping\SortableGroupInterface;
use DateTime;

/**
 * @implements SortableGroupInterface<DateTime, Lesson>
 */
class LessonDayGroup implements SortableGroupInterface {

    /** @var Lesson[] */
    private array $lessons;

    public function __construct(private readonly DateTime $date)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return Lesson[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    public function getLesson(int $lessonNumber): ?Lesson {
        foreach($this->lessons as $lesson) {
            if($lesson->getLessonNumber() === $lessonNumber) {
                return $lesson;
            }
        }

        return null;
    }

    public function getKey(): DateTime {
        return $this->date;
    }

    public function addItem($item): void {
        $this->lessons[] = $item;
    }

    public function &getItems(): array {
        return $this->lessons;
    }
}