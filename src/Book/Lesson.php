<?php

namespace App\Book;

use App\Entity\LessonEntry;
use App\Entity\Lesson as LessonEntity;
use App\Entity\Substitution;
use DateTime;

class Lesson {

    /** @var DateTime */
    private $date;

    /** @var int */
    private $lessonNumber;

    /** @var LessonEntity|null */
    private $lesson;

    /** @var LessonEntry|null */
    private $entry;

    /** @var int  */
    private $absentCount = 0;

    /** @var int  */
    private $lateCount = 0;

    /** @var int  */
    private $presentCount = 0;

    /** @var Substitution|null */
    private $substitution = null;

    public function __construct(DateTime $date, int $lessonNumber, ?LessonEntity $lesson = null, ?LessonEntry $entry = null, ?Substitution $substitution = null) {
        $this->date = $date;
        $this->lessonNumber = $lessonNumber;
        $this->lesson = $lesson;
        $this->entry = $entry;
        $this->substitution = $substitution;
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
     * @return LessonEntity|null
     */
    public function getLesson(): ?LessonEntity {
        return $this->lesson;
    }

    /**
     * @return LessonEntry|null
     */
    public function getEntry(): ?LessonEntry {
        return $this->entry;
    }

    /**
     * @param LessonEntity|null $lesson
     * @return Lesson
     */
    public function setLesson(?LessonEntity $lesson): Lesson {
        $this->lesson = $lesson;
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

    /**
     * @return int
     */
    public function getAbsentCount(): int {
        return $this->absentCount;
    }

    /**
     * @param int $absentCount
     * @return Lesson
     */
    public function setAbsentCount(int $absentCount): Lesson {
        $this->absentCount = $absentCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getLateCount(): int {
        return $this->lateCount;
    }

    /**
     * @param int $lateCount
     * @return Lesson
     */
    public function setLateCount(int $lateCount): Lesson {
        $this->lateCount = $lateCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getPresentCount(): int {
        return $this->presentCount;
    }

    /**
     * @param int $presentCount
     * @return Lesson
     */
    public function setPresentCount(int $presentCount): Lesson {
        $this->presentCount = $presentCount;
        return $this;
    }

    /**
     * @return Substitution|null
     */
    public function getSubstitution(): ?Substitution {
        return $this->substitution;
    }
}