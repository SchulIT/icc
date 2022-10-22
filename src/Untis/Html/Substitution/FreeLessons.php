<?php

namespace App\Untis\Html\Substitution;

class FreeLessons {

    public function __construct(private int $lessonStart, private int $lessonEnd)
    {
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }
}