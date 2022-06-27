<?php

namespace App\Timetable;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableSupervision;

class TimetableLessonContainer {

    /** @var int */
    private int $lesson;

    /** @var int */
    private int $rowSpan = 1;

    /** @var bool */
    private bool $isCollapsed = false;

    /**
     * @var TimetableLessonEntity[]
     */
    private array $lessons = [ ];

    /**
     * @var TimetableSupervision[]
     */
    private array $beforeSupervisions = [ ];

    /**
     * @var TimetableSupervision[]
     */
    public array $supervisions = [ ];

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
     * @return int
     */
    public function getRowSpan(): int {
        return $this->rowSpan;
    }

    /**
     * @param int $rowSpan
     * @return TimetableLessonContainer
     */
    public function setRowSpan(int $rowSpan): TimetableLessonContainer {
        $this->rowSpan = $rowSpan;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCollapsed(): bool {
        return $this->isCollapsed;
    }

    /**
     * @param bool $isCollapsed
     * @return TimetableLessonContainer
     */
    public function setIsCollapsed(bool $isCollapsed): TimetableLessonContainer {
        $this->isCollapsed = $isCollapsed;
        return $this;
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
    public function getLessons(): array {
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
    public function getSupervisions(): array {
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
    public function getBeforeSupervisions(): array {
        return $this->beforeSupervisions;
    }

    /**
     * @return bool
     */
    public function hasSupervisionBefore(): bool {
        return count($this->beforeSupervisions) > 0;
    }

    public function clear(): void {
        $this->lessons = [ ];
        $this->beforeSupervisions = [ ];
        $this->supervisions = [ ];
    }
}