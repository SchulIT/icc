<?php

namespace App\Book;

use App\Entity\LessonEntry;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;
use DateTime;

class Lesson {

    private int $absentCount = 0;
    private int $lateCount = 0;
    private int $presentCount = 0;

    public function __construct(private DateTime $date, private int $lessonNumber, private ?TimetableLesson $lesson = null, private ?LessonEntry $entry = null, private ?Substitution $substitution = null)
    {
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLessonNumber(): int {
        return $this->lessonNumber;
    }

    public function getLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    public function setLesson(?TimetableLesson $lesson): Lesson {
        $this->lesson = $lesson;
        return $this;
    }

    public function setEntry(?LessonEntry $entry): Lesson {
        $this->entry = $entry;
        return $this;
    }

    public function getAbsentCount(): int {
        return $this->absentCount;
    }

    public function setAbsentCount(int $absentCount): Lesson {
        $this->absentCount = $absentCount;
        return $this;
    }

    public function getLateCount(): int {
        return $this->lateCount;
    }

    public function setLateCount(int $lateCount): Lesson {
        $this->lateCount = $lateCount;
        return $this;
    }

    public function getPresentCount(): int {
        return $this->presentCount;
    }

    public function setPresentCount(int $presentCount): Lesson {
        $this->presentCount = $presentCount;
        return $this;
    }

    public function getSubstitution(): ?Substitution {
        return $this->substitution;
    }
}