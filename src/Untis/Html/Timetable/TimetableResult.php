<?php

namespace App\Untis\Html\Timetable;

class TimetableResult {

    private string $objective;

    private array $lessons;

    public function __construct(string $objective, array $lessons) {
        $this->objective = $objective;
        $this->lessons = $lessons;
    }

    /**
     * @return string
     */
    public function getObjective(): string {
        return $this->objective;
    }

    /**
     * @return array
     */
    public function getLessons(): array {
        return $this->lessons;
    }
}