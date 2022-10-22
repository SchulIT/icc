<?php

namespace App\View\Filter;

use App\Entity\Teacher;

class TeacherFilterView implements FilterViewInterface {

    /**
     * @param Teacher[] $teachers
     */
    public function __construct(private array $teachers, private ?Teacher $currentTeacher)
    {
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    public function getCurrentTeacher(): ?Teacher {
        return $this->currentTeacher;
    }

    public function isEnabled(): bool {
        return count($this->teachers) > 0;
    }
}