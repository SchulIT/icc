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
     * @var TimetableLessonItem[]
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
        $this->lessons[] = new TimetableLessonItem($timetableLesson->getTuition(), $timetableLesson->getRoom());
    }

    /**
     * @return TimetableLessonItem[]
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

    /**
     * @param TimetableLesson $lesson
     * @return bool
     */
    public function isSameAs(TimetableLesson $lesson): bool {
        $selector = function(TimetableLessonItem $item) {
            return $item->getTuition()->getId();
        };

        $tuitionsLeft = array_map($selector, $this->getLessons());
        $tuitionsRight = array_map($selector, $lesson->getLessons());

        $diffLeft = array_diff($tuitionsLeft, $tuitionsRight);
        $diffRight = array_diff($tuitionsRight, $tuitionsLeft);

        return count($diffLeft) === 0 && count($diffRight) === 0;
    }
}