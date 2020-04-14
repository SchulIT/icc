<?php

namespace App\View\Filter;

use App\Entity\Teacher;

class TeacherFilterView implements FilterViewInterface {

    /** @var Teacher[] */
    private $teachers;

    /** @var Teacher|null */
    private $currentTeacher;

    public function __construct(array $teachers, ?Teacher $teacher) {
        $this->teachers = $teachers;
        $this->currentTeacher = $teacher;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @return Teacher|null
     */
    public function getCurrentTeacher(): ?Teacher {
        return $this->currentTeacher;
    }

    public function isEnabled(): bool {
        return count($this->teachers) > 0;
    }
}