<?php

namespace App\View\Filter;

use App\Entity\Student;
use App\Grouping\StudentGradeGroup;

class StudentFilterView implements FilterViewInterface {

    /**
     * @param StudentGradeGroup[] $studentGradeGroups
     */
    public function __construct(private array $studentGradeGroups, private ?Student $currentStudent, private int $studentCount)
    {
    }

    /**
     * @return StudentGradeGroup[]
     */
    public function getStudentGradeGroups(): array {
        return $this->studentGradeGroups;
    }

    public function getCurrentStudent(): ?Student {
        return $this->currentStudent;
    }

    public function getStudentCount(): int {
        return $this->studentCount;
    }

    public function isEnabled(): bool {
        return count($this->studentGradeGroups) > 0;
    }
}