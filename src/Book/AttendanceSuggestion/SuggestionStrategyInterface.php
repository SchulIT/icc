<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Tuition;
use DateTime;

interface SuggestionStrategyInterface {

    /**
     * @param Tuition $tuition
     * @param DateTime $date
     * @param int $lessonStart
     * @param int $lessonEnd
     * @return PrioritizedSuggestion[]
     */
    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array;
}