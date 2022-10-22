<?php

namespace App\Untis\Html\Substitution;

class ParsedLesson {

    public function __construct(private int $lessonStart, private int $lessonEnd, private bool $isBefore)
    {
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function isBefore(): bool {
        return $this->isBefore;
    }
}