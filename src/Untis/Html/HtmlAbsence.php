<?php

namespace App\Untis\Html;

class HtmlAbsence {
    private HtmlAbsenceObjectiveType $objectiveType;

    private string $objective;

    private ?int $lessonStart;

    private ?int $lessonEnd;

    public function __construct(HtmlAbsenceObjectiveType $objectiveType, string $objective, ?int $lessonStart, ?int $lessonEnd) {
        $this->objectiveType = $objectiveType;
        $this->objective = $objective;
        $this->lessonStart = $lessonStart;
        $this->lessonEnd = $lessonEnd;
    }
    /**
     * @return HtmlAbsenceObjectiveType
     */
    public function getObjectiveType(): HtmlAbsenceObjectiveType {
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