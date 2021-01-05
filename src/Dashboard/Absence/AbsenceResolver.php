<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentStudent;
use App\Entity\Student;
use DateTime;

/**
 * Helper class which resolves absences in a effective way utilizing database queries instead of working with objects.
 * All strategies are explicitly allowed to use a dedicated QueryBuilder instead of relying on the repository methods.
 */
class AbsenceResolver {
    private $strategies;

    /**
     * @param AbsenceResolveStrategyInterface[] $strategies
     */
    public function __construct(iterable $strategies) {
        $this->strategies = $strategies;
    }

    /**
     * @param DateTime $dateTime
     * @param int $lesson
     * @param Student[] $students
     * @param string[] FQCN of excluded strategies
     * @return AbsentStudent[]
     */
    public function resolve(DateTime $dateTime, int $lesson, iterable $students, array $excludedResolvers = [ ]): array {
        $absent = [ ];

        foreach($this->strategies as $strategy) {
            if(in_array(get_class($strategy), $excludedResolvers)) {
                continue;
            }
            $absent = array_merge($absent, $strategy->resolveAbsentStudents($dateTime, $lesson, $students));
        }

        // TODO: CACHE?!

        return $absent;
    }
}