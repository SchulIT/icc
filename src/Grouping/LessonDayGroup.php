<?php

namespace App\Grouping;

use App\Book\Lesson;
use DateTime;

class LessonDayGroup implements GroupInterface, SortableGroupInterface {

    /** @var DateTime */
    private $date;

    /** @var Lesson[] */
    private $lessons;

    public function __construct(DateTime $dateTime) {
        $this->date = $dateTime;
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

    /**
     * @param int $lessonNumber
     * @return Lesson|null
     */
    public function getLesson(int $lessonNumber): ?Lesson {
        foreach($this->lessons as $lesson) {
            if($lesson->getLessonNumber() === $lessonNumber) {
                return $lesson;
            }
        }

        return null;
    }

    public function getKey() {
        return $this->date;
    }

    public function addItem($item) {
        $this->lessons[] = $item;
    }

    public function &getItems(): array {
        return $this->lessons;
    }
}