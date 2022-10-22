<?php

namespace App\View\Filter;

use App\Entity\Teacher;

class TeachersFilterView implements FilterViewInterface {

    /**
     * @param Teacher[] $teachers
     * @param Teacher[] $currentTeachers
     */
    public function __construct(private array $teachers, private array $currentTeachers)
    {
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @return Teacher[]
     */
    public function getCurrentTeachers(): array {
        return $this->currentTeachers;
    }

    public function isEnabled(): bool {
        return count($this->teachers) > 0;
    }
}