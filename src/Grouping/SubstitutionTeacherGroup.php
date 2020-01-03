<?php

namespace App\Grouping;

use App\Entity\Substitution;
use App\Entity\Teacher;

class SubstitutionTeacherGroup implements GroupInterface, SortableGroupInterface {

    /** @var Teacher|null */
    private $teacher;

    /** @var Substitution[] */
    private $substitutions = [ ];

    public function __construct(?Teacher $teacher) {
        $this->teacher = $teacher;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @return Substitution[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }

    /**
     * @return Teacher|null
     */
    public function getKey() {
        return $this->teacher;
    }

    /**
     * @param Substitution $item
     */
    public function addItem($item) {
        $this->substitutions[] = $item;
    }

    public function &getItems(): array {
        return $this->substitutions;
    }
}