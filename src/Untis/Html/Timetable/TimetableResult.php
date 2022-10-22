<?php

namespace App\Untis\Html\Timetable;

class TimetableResult {

    public function __construct(private string $objective, private array $lessons)
    {
    }

    public function getObjective(): string {
        return $this->objective;
    }

    public function getLessons(): array {
        return $this->lessons;
    }
}