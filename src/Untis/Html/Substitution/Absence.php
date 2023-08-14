<?php

namespace App\Untis\Html\Substitution;

class Absence {
    public function __construct(private AbsenceObjectiveType $objectiveType, private string $objective, private ?int $lessonStart, private ?int $lessonEnd)
    {
    }
    public function getObjectiveType(): AbsenceObjectiveType {
        return $this->objectiveType;
    }

    public function getObjective(): string {
        return $this->objective;
    }

    public function setLessonStart(?int $lessonStart): void {
        $this->lessonStart = $lessonStart;
    }

    public function setLessonEnd(?int $lessonEnd): void {
        $this->lessonEnd = $lessonEnd;
    }

    public function getLessonStart(): ?int {
        return $this->lessonStart;
    }

    public function getLessonEnd(): ?int {
        return $this->lessonEnd;
    }
}