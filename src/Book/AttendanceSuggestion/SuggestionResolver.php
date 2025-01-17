<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Tuition;
use App\Response\Book\AttendanceSuggestion;
use DateTime;

class SuggestionResolver {

    /**
     * @param iterable|SuggestionStrategyInterface[] $strategies
     */
    public function __construct(private readonly iterable $strategies) { }

    /**
     * @param Tuition $tuition
     * @param DateTime $date
     * @param int $lessonStart
     * @param int $lessonEnd
     * @param class-string<SuggestionStrategyInterface>[] $excludeStrategies Strategies to ignore - defaults to [ ]
     * @return AttendanceSuggestion[]
     */
    public function resolve(Tuition $tuition, DateTime $date, int $lessonStart, int $lessonEnd, array $excludeStrategies = [ ]): array {
        /** @var PrioritizedSuggestion[] $suggestions */
        $suggestions = [ ];

        foreach($this->strategies as $strategy) {
            if(in_array(get_class($strategy), $excludeStrategies)) {
                continue;
            }

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