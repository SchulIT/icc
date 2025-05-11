<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Tuition;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.book.attendance_suggestion_strategy')]
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