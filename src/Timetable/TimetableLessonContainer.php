<?php

namespace App\Timetable;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableSupervision;

class TimetableLessonContainer {

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

    public function __construct(private int $lesson)
    {
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function getRowSpan(): int {
        return $this->rowSpan;
    }

    public function setRowSpan(int $rowSpan): TimetableLessonContainer {
        $this->rowSpan = $rowSpan;
        return $this;
    }

    public function isCollapsed(): bool {
        return $this->isCollapsed;
    }

    public function setIsCollapsed(bool $isCollapsed): TimetableLessonContainer {
        $this->isCollapsed = $isCollapsed;
        return $this;
    }

    public function addTimetableLesson(TimetableLessonEntity $timetableLesson): void {
        $this->lessons[] = $timetableLesson;
    }

    /**
     * @return TimetableLessonEntity[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    public function addSupervision(TimetableSupervision $supervision): void {
        $this->supervisions[] = $supervision;
    }

    /**
     * @return TimetableSupervision[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }

    public function addBeforeSupervison(TimetableSupervision $entry): void {
        $this->beforeSupervisions[] = $entry;
    }

    /**
     * @return TimetableSupervision[]
     */
    public function getBeforeSupervisions(): array {
        return $this->beforeSupervisions;
    }

    public function hasSupervisionBefore(): bool {
        return count($this->beforeSupervisions) > 0;
    }

    public function clear(): void {
        $this->lessons = [ ];
        $this->beforeSupervisions = [ ];
        $this->supervisions = [ ];
    }
}