<?php

namespace App\Untis\Html;

class HtmlFreeLessons {

    private int $lessonStart;
    private int $lessonEnd;

    public function __construct(int $lessonStart, int $lessonEnd) {
        $this->lessonStart = $lessonStart;
        $this->lessonEnd = $lessonEnd;
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
}