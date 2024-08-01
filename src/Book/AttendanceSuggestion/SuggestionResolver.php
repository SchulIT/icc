<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Tuition;
use DateTime;

class SuggestionResolver {

    /**
     * @param iterable|SuggestionStrategyInterface[] $strategies
     */
    public function __construct(private readonly iterable $strategies) { }

    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd): array {
        /** @var PrioritizedSuggestion[] $suggestions */
        $suggestions = [ ];

        foreach($this->strategies as $strategy) {
            foreach ($strategy->resolve($tuition, $date, $lessonStart, $lessonEnd) as $prioritizedSuggestion) {
                if (!array_key_exists($prioritizedSuggestion->getStudent()->getId(), $suggestions)) {
                    $suggestions[$prioritizedSuggestion->getStudent()->getId()] = $prioritizedSuggestion;
                } else {
                    if ($suggestions[$prioritizedSuggestion->getStudent()->getId()]->getPriority() < $prioritizedSuggestion->getPriority()) {
                        $suggestions[$prioritizedSuggestion->getStudent()->getId()] = $prioritizedSuggestion;
                    }
                }
            }
        }

        return array_values(array_map(fn(PrioritizedSuggestion $suggestion) => $suggestion->getSuggestion(), $suggestions));
    }
}