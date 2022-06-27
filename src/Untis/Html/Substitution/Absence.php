<?php

namespace App\Untis\Html\Substitution;

class Absence {
    private AbsenceObjectiveType $objectiveType;

    private string $objective;

    private ?int $lessonStart;

    private ?int $lessonEnd;

    public function __construct(AbsenceObjectiveType $objectiveType, string $objective, ?int $lessonStart, ?int $lessonEnd) {
        $this->objectiveType = $objectiveType;
        $this->objective = $objective;
        $this->lessonStart = $lessonStart;
        $this->lessonEnd = $lessonEnd;
    }
    /**
     * @return AbsenceObjectiveType
     */
    public function getObjectiveType(): AbsenceObjectiveType {
        return $this->objectiveType;
    }

    /**
     * @return string
     */
    public function getObjective(): string {
        return $this->objective;
    }

    /**
     * @return int|null
     */
    public function getLessonStart(): ?int {
        return $this->lessonStart;
    }

    /**
     * @return int|null
     */
    public function getLessonEnd(): ?int {
        return $this->lessonEnd;
    }
}