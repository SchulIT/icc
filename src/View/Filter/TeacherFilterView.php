<?php

namespace App\View\Filter;

use App\Entity\Teacher;

class TeacherFilterView implements FilterViewInterface {

    /**
     * @param Teacher[] $teachers
     */
    public function __construct(private readonly array $teachers, private readonly ?Teacher $currentTeacher, private readonly bool $isEmpty)
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

    public function isEmpty(): bool {
        return $this->isEmpty;
    }

    public function isEnabled(): bool {
        return count($this->teachers) > 0;
    }
}