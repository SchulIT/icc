<?php

namespace App\Book;

use App\Entity\LessonEntry;
use App\Entity\TimetableLesson;
use DateTime;

class Lesson {

    /** @var DateTime */
    private $date;

    /** @var int */
    private $lessonNumber;

    /** @var TimetableLesson|null */
    private $timetableLesson;

    /** @var LessonEntry|null */
    private $entry;

    public function __construct(DateTime $date, int $lessonNumber, ?TimetableLesson $lesson = null, ?LessonEntry $entry = null) {
        $this->date = $date;
        $this->lessonNumber = $lessonNumber;
        $this->timetableLesson = $lesson;
        $this->entry = $entry;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getLessonNumber(): int {
        return $this->lessonNumber;
    }

    /**
     * @return TimetableLesson|null
     */
    public function getTimetableLesson(): ?TimetableLesson {
        return $this->timetableLesson;
    }

    /**
     * @return LessonEntry|null
     */
    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    /**
     * @param TimetableLesson|null $timetableLesson
     * @return Lesson
     */
    public function setTimetableLesson(?TimetableLesson $timetableLesson): Lesson {
        $this->timetableLesson = $timetableLesson;
        return $this;
    }

    /**
     * @param LessonEntry|null $entry
     * @return Lesson
     */
    public function setEntry(?LessonEntry $entry): Lesson {
        $this->entry = $entry;
        return $this;
    }
}