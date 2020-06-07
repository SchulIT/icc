<?php

namespace App\View\Filter;

use App\Entity\Teacher;

class TeachersFilterView implements FilterViewInterface {

    /** @var Teacher[] */
    private $teachers;

    /** @var Teacher[] */
    private $currentTeachers;

    public function __construct(array $teachers, array $currentTeachers) {
        $this->teachers = $teachers;
        $this->currentTeachers = $currentTeachers;
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