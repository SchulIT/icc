<?php

namespace App\Timetable;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableSupervision;

class TimetableLesson {

    /** @var int */
    private $lesson;

    /** @var bool Flag whether to display the next lesson with this lesson */
    private $includeNextLesson = false;

    /** @var bool Flag whether this lesson is collapsed (and thus does not need to be displayed) */
    private $isCollapsed = false;

    /**
     * @var TimetableLessonEntity[]
     */
    private $lessons = [ ];

    /**
     * @var TimetableSupervision[]
     */
    private $beforeSupervisions = [ ];

    /**
     * @var TimetableSupervision[]
     */
    public $supervisions = [ ];

    public function __construct(int $lesson) {
        $this->lesson = $lesson;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param bool $isCollapsed
     * @return TimetableLesson
     */
    public function setCollapsed(bool $isCollapsed = true): TimetableLesson {
        $this->isCollapsed = $isCollapsed;
        return $this;
    }

    /**
     * @param bool $includeNextLesson
     * @return TimetableLesson
     */
    public function setIncludeNextLesson(bool $includeNextLesson = true): TimetableLesson {
        $this->includeNextLesson = $includeNextLesson;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCollapsed(): bool {
        return $this->isCollapsed;
    }

    /**
     * @return bool
     */
    public function includeNextLesson(): bool {
        return $this->includeNextLesson;
    }

    /**
     * @param TimetableLessonEntity $timetableLesson
     */
    public function addTimetableLesson(TimetableLessonEntity $timetableLesson): void {
        $this->lessons[] = $timetableLesson;
    }

    /**
     * @return TimetableLessonEntity[]
     */
    public function getLessons() {
        return $this->lessons;
    }

    /**
     * @param TimetableSupervision $supervision
     */
    public function addSupervision(TimetableSupervision $supervision): void {
        $this->supervisions[] = $supervision;
    }

    /**
     * @return TimetableSupervision[]
     */
    public function getSupervisions() {
        return $this->supervisions;
    }

    /**
     * @param TimetableSupervision $entry
     */
    public function addBeforeSupervison(TimetableSupervision $entry): void {
        $this->beforeSupervisions[] = $entry;
    }

    /**
     * @return TimetableSupervision[]
     */
    public function getBeforeSupervisions() {
        return $this->beforeSupervisions;
    }

    /**
     * @return bool
     */
    public function hasSupervisionBefore(): bool {
        return count($this->beforeSupervisions) > 0;
    }
}