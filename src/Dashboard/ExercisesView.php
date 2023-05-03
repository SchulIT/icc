<?php

namespace App\Dashboard;

use App\Entity\Grade;
use App\Entity\LessonEntry;
use DateTime;

class ExercisesView {

    /** @var LessonEntry[] */
    private array $entriesWithExercise = [ ];

    public function __construct(private readonly DateTime $start, private readonly DateTime $end, private readonly Grade $grade) {

    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @return Grade
     */
    public function getGrade(): Grade {
        return $this->grade;
    }

    public function setEntriesWithExercises(array $entries): void {
        $this->entriesWithExercise = $entries;
    }

    /**
     * @return array
     */
    public function getEntriesWithExercise(): array {
        return $this->entriesWithExercise;
    }
}