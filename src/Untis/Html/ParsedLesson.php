<?php

namespace App\Untis\Html;

class ParsedLesson {

    private int $lessonStart;

    private int $lessonEnd;

    private bool $isBefore;

    public function __construct(int $lessonStart, int $lessonEnd, bool $isBefore) {
        $this->lessonStart = $lessonStart;
        $this->lessonEnd = $lessonEnd;
        $this->isBefore = $isBefore;
    }

    /**
     * @return int
     */
    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    /**
     * @return int
     */
    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    /**
     * @return bool
     */
    public function isBefore(): bool {
        return $this->isBefore;
    }
}