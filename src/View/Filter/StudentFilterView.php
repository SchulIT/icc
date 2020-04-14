<?php

namespace App\View\Filter;

use App\Entity\Student;
use App\Grouping\StudentGradeGroup;

class StudentFilterView implements FilterViewInterface {

    /** @var StudentGradeGroup[] */
    private $studentGradeGroups;

    /** @var Student|null */
    private $currentStudent;

    public function __construct(array $studentGradeGroups, ?Student $currentStudent) {
        $this->studentGradeGroups = $studentGradeGroups;
        $this->currentStudent = $currentStudent;
    }

    /**
     * @return StudentGradeGroup[]
     */
    public function getStudentGradeGroups(): array {
        return $this->studentGradeGroups;
    }

    /**
     * @return Student|null
     */
    public function getCurrentStudent(): ?Student {
        return $this->currentStudent;
    }

    public function isEnabled(): bool {
        return count($this->studentGradeGroups) > 0;
    }
}